<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\Config;

/**
 * Class CheckMessageQueueStoppedConfig
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\Config
 */
class CheckMessageQueueStoppedConfig extends AbstractCheckMessageQueueConfig {
  const OPTION_MAX_HANDLE_TIME = 'max_handle_time';

  private int $maxHandleTime;

  /**
   * @inheritDoc
   */
  public static function createFromConfigArray(array $configArray): self {
    $config = parent::createFromConfigArray($configArray);
    $config->maxHandleTime = $configArray[self::OPTION_MAX_HANDLE_TIME];
    return $config;
  }

  /**
   * @return int
   */
  public function getMaxHandleTime(): int {
    return $this->maxHandleTime;
  }

}
