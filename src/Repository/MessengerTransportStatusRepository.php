<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatus;

/**
 * @method MessengerTransportStatus|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessengerTransportStatus|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessengerTransportStatus[]    findAll()
 * @method MessengerTransportStatus[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MessengerTransportStatusRepository extends ServiceEntityRepository {

  /**
   * MessengerTransportStatusRepository constructor.
   */
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, MessengerTransportStatus::class);
  }

  public function getMessengerTransportStatusByTransport(string $transport): ?MessengerTransportStatus {
    return $this->findOneBy(['transport' => $transport]);
  }

  /**
   * @return Collection<MessengerTransportStatus>
   */
  public function getMessengerTransportStatues(): Collection {
    return new ArrayCollection($this->findAll());
  }

}
