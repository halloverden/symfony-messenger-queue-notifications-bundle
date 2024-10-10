<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatus;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\MessengerTransportStatusRepository;
use Symfony\Component\Lock\Exception\ExceptionInterface;
use Symfony\Component\Lock\Exception\LockReleasingException;
use Symfony\Component\Lock\LockFactory;

final readonly class MessengerTransportStatusService {
  private EntityManagerInterface $entityManager;

  /**
   * MessengerTransportStatusService constructor.
   */
  public function __construct(
    private MessengerTransportStatusRepository $messengerTransportStatusRepository,
    private LockFactory                        $lockFactory,
    ManagerRegistry                            $registry,
  ) {
    $this->entityManager = $registry->getManagerForClass(MessengerTransportStatus::class);
  }

  public function getMessengerTransportStatus(string $transport): ?MessengerTransportStatus {
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

      return $status;
    } finally {
      try {
        $lock->release();
      } catch (LockReleasingException) {
      }
    }
  }

}
