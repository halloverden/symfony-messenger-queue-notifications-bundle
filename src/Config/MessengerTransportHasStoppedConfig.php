<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Config;

final readonly class MessengerTransportHasStoppedConfig extends AbstractMessengerTransportStatusEventConfig {

  /**
   * MessengerTransportHasStoppedConfig constructor.
   *
   * @param \DateInterval[] $eventIntervals
   */
  public function __construct(
    array                $eventIntervals,
    string               $transport,
    public \DateInterval $maxWorkerRunningInterval
  ) {
    parent::__construct($eventIntervals, $transport);
  }

  /**
   * @param string[] $eventIntervalStrings
   * @param string   $transport
   * @param string   $maxWorkerRunningIntervalString
   *
   * @return self
   */
  public static function create(array $eventIntervalStrings, string $transport, string $maxWorkerRunningIntervalString): self {
    return new self(
      \array_map(fn(string $interval) => \DateInterval::createFromDateString($interval), $eventIntervalStrings),
      $transport,
      \DateInterval::createFromDateString($maxWorkerRunningIntervalString)
    );
  }

}
