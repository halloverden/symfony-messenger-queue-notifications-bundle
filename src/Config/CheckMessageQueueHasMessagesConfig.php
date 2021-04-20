<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\Config;

/**
 * Class CheckMessageQueueHasMessagesConfig
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\Config
 */
class CheckMessageQueueHasMessagesConfig extends AbstractCheckMessageQueueConfig {
  const OPTION_MAX_MESSAGES = 'max_messages';

  private int $maxMessages;


  /**
   * @inheritDoc
   */
  public static function createFromConfigArray(array $configArray): self {
    $config = parent::createFromConfigArray($configArray);
    $config->maxMessages = $configArray[self::OPTION_MAX_MESSAGES];
    return $config;
  }

  /**
   * @return int
   */
  public function getMaxMessages(): int {
    return $this->maxMessages;
  }

}
