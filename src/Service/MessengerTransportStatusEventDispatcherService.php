<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueNotificationsBundle\Config\AbstractMessengerTransportStatusEventConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Config\MessengerTransportHasMessagesConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Config\MessengerTransportHasStoppedConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatus;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatusEventSent;
use HalloVerden\MessengerQueueNotificationsBundle\Event\MessengerTransportHasMessagesEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Event\MessengerTransportHasStoppedEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\MessengerTransportStatusEventSentRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\ClockInterface;

final readonly class MessengerTransportStatusEventDispatcherService {
  private EntityManagerInterface $entityManager;


  /**
   * MessengerTransportStatusEventDispatcherService constructor.
   *
   * @param AbstractMessengerTransportStatusEventConfig[] $eventConfigs
   */
  public function __construct(
    private array                                       $eventConfigs,
    private MessengerTransportStatusService             $messengerTransportStatusService,
    private MessengerTransportInfoService               $infoService,
    private MessengerTransportStatusEventSentRepository $statusEventSentRepository,
    private EventDispatcherInterface                    $eventDispatcher,
    private ClockInterface                              $clock,
    private ManagerRegistry                             $registry,
    private ?LoggerInterface                            $logger
  ) {
    $this->entityManager = $this->registry->getManagerForClass(MessengerTransportStatusEventSent::class);
  }

  /**
   * @return void
   * @throws \DateInvalidOperationException|Exception
   */
  public function dispatchTransportStatusEvents(): void {
    foreach ($this->eventConfigs as $eventConfig) {
      $this->dispatchTransportStatusEvent($eventConfig);
    }
  }

  /**
   * @param AbstractMessengerTransportStatusEventConfig $config
   *
   * @return void
   * @throws \DateInvalidOperationException|Exception
   */
  private function dispatchTransportStatusEvent(AbstractMessengerTransportStatusEventConfig $config): void {
    $status = $this->messengerTransportStatusService->getMessengerTransportStatus($config->transport);
    if (null === $status) {
      return;
    }

    $status->setLastAvailableMessageCount($this->infoService->getMessageCount($config->transport));
    $this->entityManager->flush();

    if ($config instanceof MessengerTransportHasMessagesConfig) {
      $this->dispatchTransportHasMessagesEvent($config, $status);
    }

    if ($config instanceof MessengerTransportHasStoppedConfig) {
      $this->dispatchTransportHasStoppedEvent($config, $status);
    }
  }

  /**
   * @param MessengerTransportHasMessagesConfig $config
   * @param MessengerTransportStatus            $status
   *
   * @return void
   * @throws \DateInvalidOperationException|Exception
   */
  private function dispatchTransportHasMessagesEvent(MessengerTransportHasMessagesConfig $config, MessengerTransportStatus $status): void {
    if ($status->getLastAvailableMessageCount() <= $config->maxMessages) {
      $this->logger?->debug('{event} for transport "{transport}" not dispatched since message count ({messageCount}) has not reached max messages ({maxMessages})', [
        'event' => MessengerTransportHasMessagesEvent::class,
        'transport' => $config->transport,
        'messageCount' => $status->getLastAvailableMessageCount(),
        'maxMessages' => $config->maxMessages
      ]);
      $this->statusEventSentRepository->deleteStatusEvents($config->transport, MessengerTransportHasMessagesEvent::class);
      return;
    }

    if ($this->isEventRecentlySent($config->transport, MessengerTransportHasMessagesEvent::class, $config->eventIntervals, $lastSentAt, $sentCount)) {
      $this->logger?->info('{event} for transport "{transport}" not dispatched since it\'s recently sent ({lastSentAt})', [
        'event' => MessengerTransportHasMessagesEvent::class,
        'transport' => $config->transport,
        'lastSentAt' => $lastSentAt?->format(\DateTimeInterface::ATOM) ?? 'never'
      ]);
      return;
    }

    $this->eventDispatcher->dispatch($event = new MessengerTransportHasMessagesEvent($config, $status, $sentCount + 1));
    $this->createStatusEventSent($config->transport, $event);
    $this->logger?->info('{event} for transport "{transport}" dispatched', [
      'event' => $event::class,
      'transport' => $config->transport
    ]);
  }

  /**
   * @param MessengerTransportHasStoppedConfig $config
   * @param MessengerTransportStatus           $status
   *
   * @return void
   * @throws \DateInvalidOperationException|Exception
   */
  private function dispatchTransportHasStoppedEvent(MessengerTransportHasStoppedConfig $config, MessengerTransportStatus $status): void {
    if ($status->getLastWorkerRunningAt() >= ($maxWorkerRunningAt = $this->clock->now()->sub($config->maxWorkerRunningInterval))) {
      $this->logger?->debug('{event} for transport "{transport}" not dispatched since last worker running at {lastWorkerRunningAt} is after {maxWorkerRunningAt}', [
        'event' => MessengerTransportHasStoppedEvent::class,
        'transport' => $config->transport,
        'lastWorkerRunningAt' => $status->getLastWorkerRunningAt()?->format(\DateTimeInterface::ATOM) ?? 'never',
        'maxWorkerRunningAt' => $maxWorkerRunningAt->format(\DateTimeInterface::ATOM)
      ]);
      $this->statusEventSentRepository->deleteStatusEvents($config->transport, MessengerTransportHasStoppedEvent::class);
      return;
    }

    if ($this->isEventRecentlySent($config->transport, MessengerTransportHasStoppedEvent::class, $config->eventIntervals, $lastSentAt, $sentCount)) {
      $this->logger?->info('{event} for transport "{transport}" not dispatched since it\'s recently sent ({lastSentAt})', [
        'event' => MessengerTransportHasStoppedEvent::class,
        'transport' => $config->transport,
        'lastSentAt' => $lastSentAt?->format(\DateTimeInterface::ATOM) ?? 'never'
      ]);
      return;
    }

    $this->eventDispatcher->dispatch($event = new MessengerTransportHasStoppedEvent($config, $status, $sentCount + 1));
    $this->createStatusEventSent($config->transport, $event);
    $this->logger?->info('{event} for transport "{transport}" dispatched', [
      'event' => $event::class,
      'transport' => $config->transport
    ]);
  }

  /**
   * @param string $transport
   * @param object $event
   *
   * @return void
   */
  private function createStatusEventSent(string $transport, object $event): void {
    $statusEventSent = new MessengerTransportStatusEventSent($transport, $event::class, $this->clock->now());
    $this->entityManager->persist($statusEventSent);
    $this->entityManager->flush();
  }

  /**
   * @param string                  $transport
   * @param string                  $event
   * @param \DateInterval[]         $intervals
   * @param \DateTimeImmutable|null $lastSentAt
   * @param int|null                $count
   *
   * @return bool
   * @throws Exception
   */
  private function isEventRecentlySent(string $transport, string $event, array $intervals, ?\DateTimeImmutable & $lastSentAt = null, ?int & $count = null): bool {
    $count = $this->statusEventSentRepository->getStatusEventsSentCount($transport, $event);

    if (0 === $count) {
      return false;
    }

    $lastSentAt = $this->statusEventSentRepository->getLastStatusEventSentAt($transport, $event);
    if (null === $lastSentAt) {
      return false;
    }

    return $lastSentAt >= $this->clock->now()->sub($this->getInterval($intervals, $count));
  }

  /**
   * @param \DateInterval[] $intervals
   * @param int             $count
   *
   * @return \DateInterval
   */
  private function getInterval(array $intervals, int $count): \DateInterval {
    if (0 === \count($intervals)) {
      return new \DateInterval('PT0S'); // 0 Seconds
    }

    if (\array_key_exists($key = $count - 1, $intervals)) {
      return $intervals[$key];
    }

    return $intervals[\array_key_last($intervals)];
  }

}
