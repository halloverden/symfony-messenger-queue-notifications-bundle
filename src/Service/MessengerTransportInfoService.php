<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\Transport\Receiver\MessageCountAwareInterface;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

final readonly class MessengerTransportInfoService {

  /**
   * MessengerTransportInfoService constructor.
   */
  public function __construct(
    private ContainerInterface $receiverLocator
  ) {
  }

  /**
   * @param string $transport
   *
   * @return int|null
   */
  public function getMessageCount(string $transport): ?int {
    $receiver = $this->getReceiver($transport);

    if (!$receiver instanceof MessageCountAwareInterface) {
      return null;
    }

    return $receiver->getMessageCount();
  }


  /**
   * @param string $transport
   *
   * @return ReceiverInterface|null
   * @noinspection PhpDocMissingThrowsInspection
   */
  private function getReceiver(string $transport): ?ReceiverInterface {
    if (!$this->receiverLocator->has($transport)) {
      return null;
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    return $this->receiverLocator->get($transport);
  }

}
