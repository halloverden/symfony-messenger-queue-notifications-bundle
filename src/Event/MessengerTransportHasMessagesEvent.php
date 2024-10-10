<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Event;

use HalloVerden\MessengerQueueNotificationsBundle\Config\MessengerTransportHasMessagesConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatus;
use Symfony\Contracts\EventDispatcher\Event;

final class MessengerTransportHasMessagesEvent extends Event {

  /**
   * MessengerTransportHasMessagesEvent constructor.
   */
  public function __construct(
    public readonly MessengerTransportHasMessagesConfig $config,
    public readonly MessengerTransportStatus            $status,
    public int                                          $eventSentCount
  ) {
  }

}
