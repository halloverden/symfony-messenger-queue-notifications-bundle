<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\Event;

use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
use HalloVerden\MessengerQueueNotificationsBundle\Config\AbstractCheckMessageQueueConfig;

/**
 * Class AbstractMessageQueueEvent
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\Event
 */
abstract class AbstractMessageQueueEvent {
  private int $count;
  private MessageQueueEvent $messageQueueEvent;
  private AbstractCheckMessageQueueConfig $config;

  /**
   * MessageQueueHasMessagesEvent constructor.
   *
   * @param MessageQueueEvent $messageQueueEvent
   */
  public function __construct(MessageQueueEvent $messageQueueEvent, AbstractCheckMessageQueueConfig $config) {
    $this->messageQueueEvent = $messageQueueEvent;
    $this->config = $config;
  }

  /**
   * @return int
   */
  public function getCount(): int {
    return $this->count;
  }

  /**
   * @internal
   *
   * @param int $count
   *
   * @return AbstractMessageQueueEvent
   */
  public function setCount(int $count): self {
    $this->count = $count;
    return $this;
  }

  /**
   * @return MessageQueueEvent
   */
  public function getMessageQueueEvent(): MessageQueueEvent {
    return $this->messageQueueEvent;
  }

  /**
   * @return AbstractCheckMessageQueueConfig
   */
  public function getConfig(): AbstractCheckMessageQueueConfig {
    return $this->config;
  }

}
