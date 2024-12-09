<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatus;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\MessengerTransportStatusRepository;
use Symfony\Component\Lock\Exception\ExceptionInterface;
use Symfony\Component\Lock\Exception\LockReleasingException;
use Symfony\Component\Lock\LockFactory;

final class MessengerTransportStatusService {
  private EntityManagerInterface $entityManager;

  /**
   * @var string[]
   */
  private array $createdTransportStatuses = [];

  /**
   * MessengerTransportStatusService constructor.
   */
  public function __construct(
    private readonly MessengerTransportStatusRepository $messengerTransportStatusRepository,
    private readonly LockFactory                        $lockFactory,
    ManagerRegistry                                     $registry,
  ) {
    $this->entityManager = $registry->getManagerForClass(MessengerTransportStatus::class);
  }

  public function getMessengerTransportStatus(string $transport): ?MessengerTransportStatus {
    if (\in_array($transport, $this->createdTransportStatuses, true)) {
      return $this->messengerTransportStatusRepository->getMessengerTransportStatusByTransport($transport);
    }

    $lock = $this->lockFactory->createLock('_get_messenger_transport_status_' . $transport, 5);

    try {
      $lock->acquire(true);
    } catch (ExceptionInterface) {
      return null;
    }

    try {
      $status = $this->messengerTransportStatusRepository->getMessengerTransportStatusByTransport($transport);
      if (null === $status) {
        $status = new MessengerTransportStatus($transport);
        $this->entityManager->persist($status);
        $this->entityManager->flush();
      }

      $this->createdTransportStatuses[] = $transport;
      return $status;
    } finally {
      try {
        $lock->release();
      } catch (LockReleasingException) {
      }
    }
  }

}
