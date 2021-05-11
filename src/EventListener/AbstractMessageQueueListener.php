<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\EventListener;


use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Config\AbstractCheckMessageQueueConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\EventSent;
use HalloVerden\MessengerQueueNotificationsBundle\Event\AbstractMessageQueueEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\EventSentRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AbstractMessageQueueListener
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\EventListener
 */
abstract class AbstractMessageQueueListener implements EventSubscriberInterface {
  private EventSentRepositoryInterface $eventSentRepository;
  private EventDispatcherInterface $eventDispatcher;

  /**
   * @var AbstractCheckMessageQueueConfig[]
   */
  private array $configs;

  /**
   * AbstractMessageQueueListener constructor.
   */
  public function __construct(EventSentRepositoryInterface $eventSentRepository, EventDispatcherInterface $eventDispatcher, array $configs) {
    $this->eventSentRepository = $eventSentRepository;
    $this->eventDispatcher = $eventDispatcher;
    $this->configs = static::getConfigClass()::createFromConfigsArray($configs);
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents(): array {
    return [
      MessageQueueEvent::class => 'onMessageQueueEvent'
    ];
  }

  /**
   * @return AbstractCheckMessageQueueConfig|string
   */
  abstract protected static function getConfigClass(): string;

  /**
   * @param MessageQueueEvent               $event
   * @param AbstractCheckMessageQueueConfig $config
   *
   * @return AbstractMessageQueueEvent
   */
  abstract protected function createMessageQueueEvent(MessageQueueEvent $event, AbstractCheckMessageQueueConfig $config): AbstractMessageQueueEvent;

  /**
   * @param MessageQueueEvent               $event
   * @param AbstractCheckMessageQueueConfig $config
   *
   * @return bool
   */
  abstract protected function shouldTakeAction(MessageQueueEvent $event, AbstractCheckMessageQueueConfig $config): bool;

  /**
   * @param MessageQueueEvent $event
   */
  public function onMessageQueueEvent(MessageQueueEvent $event): void {
    $config = $this->configs[$event->getTransport()] ?? null;
    if (null === $config) {
      return;
    }

    $messageQueueEvent = $this->createMessageQueueEvent($event, $config);

    $eventSent = $this->eventSentRepository->getByEventAndTransport(get_class($messageQueueEvent), $event->getTransport());

    if ($this->shouldTakeAction($event, $config)) {
      $this->dispatchUnlessRecentlySentOrMaxEvents($messageQueueEvent, $eventSent, $config);
    } elseif (null !== $eventSent) {
      $this->eventSentRepository->delete($eventSent);
    }
  }

  /**
   * @param AbstractMessageQueueEvent       $messageQueueEvent
   * @param EventSent|null                  $eventSent
   * @param AbstractCheckMessageQueueConfig $config
   */
  protected function dispatchUnlessRecentlySentOrMaxEvents(AbstractMessageQueueEvent $messageQueueEvent, ?EventSent $eventSent, AbstractCheckMessageQueueConfig $config): void {
    if (null === $eventSent) {
      $this->eventDispatcher->dispatch($messageQueueEvent->setCount(1));
      $this->eventSentRepository->create(new EventSent(\get_class($messageQueueEvent), $messageQueueEvent->getMessageQueueEvent()->getTransport()));
      return;
    }

    if ($eventSent->getCount() >= $config->getMaxEvents()) {
      return;
    }

    if (time() - $eventSent->getLastSent()->getTimestamp() >= $config->getEventInterval()) {
      $this->eventDispatcher->dispatch($messageQueueEvent->setCount($eventSent->getCount() + 1));
      $this->eventSentRepository->updateLastSentNow($eventSent);
    }
  }

}
