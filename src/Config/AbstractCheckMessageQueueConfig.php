<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\Config;


abstract class AbstractCheckMessageQueueConfig {
  const OPTION_EVENT_INTERVAL = 'event_interval';
  const OPTION_MAX_EVENTS = 'max_events';

  private int $eventInterval;
  private int $maxEvents;

  /**
   * CheckMessageQueueStoppedConfig constructor.
   *
   * @param int $maxHandleTime
   * @param int $eventInterval
   */
  public function __construct(int $eventInterval, int $maxEvents) {
    $this->eventInterval = $eventInterval;
    $this->maxEvents = $maxEvents;
  }

  /**
   * @param array $configArray
   *
   * @return static
   */
  public static function createFromConfigArray(array $configArray): self {
    return new static(
      $configArray[self::OPTION_EVENT_INTERVAL],
      $configArray[self::OPTION_MAX_EVENTS],
    );
  }

  /**
   * @param array $configsArray
   *
   * @return array
   */
  public static function createFromConfigsArray(array $configsArray): array {
    $configs = [];

    foreach ($configsArray as $transport => $configArray) {
      $configs[$transport] = static::createFromConfigArray($configArray);
    }

    return $configs;
  }

  /**
   * @return int
   */
  public function getEventInterval(): int {
    return $this->eventInterval;
  }

  /**
   * @return int
   */
  public function getMaxEvents(): int {
    return $this->maxEvents;
  }
}
