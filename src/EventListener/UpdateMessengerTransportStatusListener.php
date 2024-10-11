<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatus;
use HalloVerden\MessengerQueueNotificationsBundle\Service\MessengerTransportInfoService;
use HalloVerden\MessengerQueueNotificationsBundle\Service\MessengerTransportStatusService;
use Symfony\Component\Clock\ClockInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\Event\SendMessageToTransportsEvent;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Event\WorkerMessageReceivedEvent;
use Symfony\Component\Messenger\Event\WorkerRateLimitedEvent;
use Symfony\Component\Messenger\Event\WorkerRunningEvent;
use Symfony\Component\Messenger\Event\WorkerStartedEvent;
use Symfony\Component\Messenger\Event\WorkerStoppedEvent;

final readonly class UpdateMessengerTransportStatusListener implements EventSubscriberInterface {
  private EntityManagerInterface $entityManager;

  /**
   * UpdateMessengerTransportStatusListener constructor.
   */
  public function __construct(
    private MessengerTransportStatusService $messengerTransportStatusService,
    private MessengerTransportInfoService   $messengerTransportInfoService,
    private ClockInterface                  $clock,
    ManagerRegistry                         $registry,
  ) {
    $this->entityManager = $registry->getManagerForClass(MessengerTransportStatus::class);
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return [
      SendMessageToTransportsEvent::class => 'onSendMessageToTransport',
      WorkerMessageFailedEvent::class => 'onWorkerMessageFailed',
      WorkerMessageHandledEvent::class => 'onWorkerMessageHandled',
      WorkerMessageReceivedEvent::class => 'onWorkerMessageReceived',
      WorkerRateLimitedEvent::class => 'onWorkerRateLimited',
      WorkerRunningEvent::class => 'onWorkerRunning',
      WorkerStartedEvent::class => 'onWorkerStartedEvent',
      WorkerStoppedEvent::class => 'onWorkerStoppedEvent'
    ];
  }

  /**
   * @param SendMessageToTransportsEvent $event
   *
   * @return void
   */
  public function onSendMessageToTransport(SendMessageToTransportsEvent $event): void {
    foreach (\array_keys($event->getSenders()) as $transport) {
      $this->getMessengerTransportStatus($transport)
        ?->setLastMessageSentAt($this->clock->now());
    }

    $this->entityManager->flush();
  }

  /**
   * @param WorkerMessageFailedEvent $event
   *
   * @return void
   */
  public function onWorkerMessageFailed(WorkerMessageFailedEvent $event): void {
    if ($event->willRetry()) {
      return;
    }

    $this->getMessengerTransportStatus($event->getReceiverName())
      ?->setLastMessageFailedAt($this->clock->now());
    $this->entityManager->flush();
  }

  /**
   * @param WorkerMessageHandledEvent $event
   *
   * @return void
   */
  public function onWorkerMessageHandled(WorkerMessageHandledEvent $event): void {
    $this->getMessengerTransportStatus($event->getReceiverName())
      ?->setLastMessageHandledAt($this->clock->now());
    $this->entityManager->flush();
  }

  /**
   * @param WorkerMessageReceivedEvent $event
   *
   * @return void
   */
  public function onWorkerMessageReceived(WorkerMessageReceivedEvent $event): void {
    $this->getMessengerTransportStatus($event->getReceiverName())
      ?->setLastMessageReceivedAt($this->clock->now());
    $this->entityManager->flush();
  }

  /**
   * @param WorkerRateLimitedEvent $event
   *
   * @return void
   */
  public function onWorkerRateLimited(WorkerRateLimitedEvent $event): void {
    $this->getMessengerTransportStatus($event->getTransportName())
      ?->setLastWorkerRateLimitedAt($this->clock->now())
      ->setLastWorkerRateLimitedUntil($event->getLimiter()->consume(0)->getRetryAfter());
  }

  /**
   * @param WorkerRunningEvent $event
   *
   * @return void
   */
  public function onWorkerRunning(WorkerRunningEvent $event): void {
    foreach ($event->getWorker()->getMetadata()->getTransportNames() as $transport) {
      $status = $this->getMessengerTransportStatus($transport)
        ?->setLastWorkerRunningAt($this->clock->now());

      // When the worker is not idle, it have just handled a message (failed or not). This is a good time to update the message count.
      if (null !== $status && !$event->isWorkerIdle()) {
        $status->setLastAvailableMessageCount($this->getMessageCount($transport));
      }
    }

    $this->entityManager->flush();
  }

  /**
   * @param WorkerStartedEvent $event
   *
   * @return void
   */
  public function onWorkerStartedEvent(WorkerStartedEvent $event): void {
    foreach ($event->getWorker()->getMetadata()->getTransportNames() as $transport) {
      $this->getMessengerTransportStatus($transport)
        ?->setLastWorkerStartedAt($this->clock->now())
        ->setLastAvailableMessageCount($this->getMessageCount($transport));
    }

    $this->entityManager->flush();
  }

  /**
   * @param WorkerStoppedEvent $event
   *
   * @return void
   */
  public function onWorkerStoppedEvent(WorkerStoppedEvent $event): void {
    foreach ($event->getWorker()->getMetadata()->getTransportNames() as $transport) {
      $this->getMessengerTransportStatus($transport)
        ?->setLastWorkerStoppedAt($this->clock->now())
        ->setLastAvailableMessageCount($this->getMessageCount($transport));
    }

    $this->entityManager->flush();
  }

  /**
   * @param string $transport
   *
   * @return MessengerTransportStatus|null
   */
  private function getMessengerTransportStatus(string $transport): ?MessengerTransportStatus {
    return $this->messengerTransportStatusService->getMessengerTransportStatus($transport);
  }

  /**
   * @param string $transport
   *
   * @return int|null
   */
  private function getMessageCount(string $transport): ?int {
    return $this->messengerTransportInfoService->getMessageCount($transport);
  }

}
