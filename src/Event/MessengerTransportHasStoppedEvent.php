<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Event;

use HalloVerden\MessengerQueueNotificationsBundle\Config\MessengerTransportHasStoppedConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatus;
use Symfony\Contracts\EventDispatcher\Event;

final class MessengerTransportHasStoppedEvent extends Event {

  /**
   * MessengerTransportHasStoppedEvent constructor.
   */
  public function __construct(
    public readonly MessengerTransportHasStoppedConfig $config,
    public readonly MessengerTransportStatus           $status,
    public int                                         $eventSentCount,
  ) {
  }

}
