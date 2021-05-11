<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\EventListener;


use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Config\AbstractCheckMessageQueueConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Config\CheckMessageQueueHasMessagesConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Event\AbstractMessageQueueEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Event\MessageQueueHasMessagesEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\EventSentRepositoryInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class CheckMessageQueueHasMessagesListener
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\EventListener
 */
class CheckMessageQueueHasMessagesListener extends AbstractMessageQueueListener {

  /**
   * CheckMessageQueueHasMessagesListener constructor.
   *
   * @param EventSentRepositoryInterface $eventSentRepository
   * @param EventDispatcherInterface     $eventDispatcher
   * @param array                        $configs
   */
  public function __construct(EventSentRepositoryInterface $eventSentRepository, EventDispatcherInterface $eventDispatcher, array $configs) {
    parent::__construct($eventSentRepository, $eventDispatcher, $configs);
  }

  /**
   * @inheritDoc
   */
  protected static function getConfigClass(): string {
    return CheckMessageQueueHasMessagesConfig::class;
  }

  /**
   * @inheritDoc
   */
  protected function createMessageQueueEvent(MessageQueueEvent $event, AbstractCheckMessageQueueConfig $config): AbstractMessageQueueEvent {
    return new MessageQueueHasMessagesEvent($event, $config);
  }

  /**
   * @inheritDoc
   */
  protected function shouldTakeAction(MessageQueueEvent $event, AbstractCheckMessageQueueConfig $config): bool {
    return $config instanceof CheckMessageQueueHasMessagesConfig
      && $event->getMessageCount() > $config->getMaxMessages();
  }

}
