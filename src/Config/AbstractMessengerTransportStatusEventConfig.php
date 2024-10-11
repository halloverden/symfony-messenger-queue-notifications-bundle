<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Config;

/**
 * @internal
 */
readonly abstract class AbstractMessengerTransportStatusEventConfig {

  /**
   * AbstractMessengerTransportStatusEventConfig constructor.
   *
   * @param \DateInterval[] $eventIntervals
   */
  public function __construct(
    public array $eventIntervals,
    public string $transport,
  ) {
  }

}
