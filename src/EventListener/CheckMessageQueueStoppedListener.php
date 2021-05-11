<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\EventListener;


use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Config\AbstractCheckMessageQueueConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Config\CheckMessageQueueStoppedConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Event\AbstractMessageQueueEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Event\MessageQueueStoppedEvent;

/**
 * Class CheckMessageQueueStoppedListener
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\EventListener
 */
class CheckMessageQueueStoppedListener extends AbstractMessageQueueListener {

  /**
   * @inheritDoc
   */
  protected static function getConfigClass(): string {
    return CheckMessageQueueStoppedConfig::class;
  }

  /**
   * @inheritDoc
   */
  protected function createMessageQueueEvent(MessageQueueEvent $event, AbstractCheckMessageQueueConfig $config): AbstractMessageQueueEvent {
    return new MessageQueueStoppedEvent($event, $config);
  }

  /**
   * @inheritDoc
   */
  protected function shouldTakeAction(MessageQueueEvent $event, AbstractCheckMessageQueueConfig $config): bool {
    $firstAvailableMessage = $event->getFirstAvailableMessage();

    return $config instanceof CheckMessageQueueStoppedConfig
      && null !== $firstAvailableMessage
      && (time() - $firstAvailableMessage->getAvailableAt()->getTimestamp()) > $config->getMaxHandleTime();
  }

}
