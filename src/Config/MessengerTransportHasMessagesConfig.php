<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Config;

final readonly class MessengerTransportHasMessagesConfig extends AbstractMessengerTransportStatusEventConfig {

  /**
   * MessengerTransportHasMessagesConfig constructor.
   *
   * @param \DateInterval[] $eventIntervals
   */
  public function __construct(
    array      $eventIntervals,
    string     $transport,
    public int $maxMessages
  ) {
    parent::__construct($eventIntervals, $transport);
  }

  /**
   * @param string[] $eventIntervalStrings
   * @param string   $transport
   * @param int      $maxMessages
   *
   * @return self
   */
  public static function create(array $eventIntervalStrings, string $transport, int $maxMessages): self {
    return new self(
      \array_map(fn(string $interval) => \DateInterval::createFromDateString($interval), $eventIntervalStrings),
      $transport,
      $maxMessages
    );
  }


}
