<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\MessengerTransportStatusEventSent;

/**
 * @method MessengerTransportStatusEventSent|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessengerTransportStatusEventSent|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessengerTransportStatusEventSent[]    findAll()
 * @method MessengerTransportStatusEventSent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class MessengerTransportStatusEventSentRepository extends ServiceEntityRepository {

  /**
   * MessengerTransportStatusEventSentRepository constructor.
   */
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, MessengerTransportStatusEventSent::class);
  }

  /**
   * @param string $transport
   * @param string $event
   *
   * @return int
   */
  public function getStatusEventsSentCount(string $transport, string $event): int {
    $qb = $this->getEntityManager()->createQueryBuilder();

    $qb
      ->select('COUNT(ses.id)')
      ->from(MessengerTransportStatusEventSent::class, 'ses')
      ->andWhere($qb->expr()->eq('ses.transport', ':transport'))
      ->andWhere($qb->expr()->eq('ses.event', ':event'))
      ->setParameter('transport', $transport)
      ->setParameter('event', $event);

    return (int) $qb->getQuery()->getSingleScalarResult();
  }

  /**
   * @param string $transport
   * @param string $event
   *
   * @return \DateTimeImmutable|null
   * @throws Exception
   */
  public function getLastStatusEventSentAt(string $transport, string $event): ?\DateTimeImmutable {
    $qb = $this->getEntityManager()->createQueryBuilder();

    $qb
      ->select('ses.sentAt')
      ->from(MessengerTransportStatusEventSent::class, 'ses')
      ->andWhere($qb->expr()->eq('ses.transport', ':transport'))
      ->andWhere($qb->expr()->eq('ses.event', ':event'))
      ->setMaxResults(1)
      ->orderBy('ses.sentAt', 'DESC')
      ->setParameter('transport', $transport)
      ->setParameter('event', $event);

    $type = Type::getType(Types::DATETIME_IMMUTABLE);
    $platform = $this->getEntityManager()->getConnection()->getDatabasePlatform();

    try {
      return $type->convertToPHPValue($qb->getQuery()->getSingleScalarResult(), $platform);
    } catch (NoResultException) {
      return null;
    }
  }

  /**
   * @param string $transport
   * @param string $event
   *
   * @return void
   */
  public function deleteStatusEvents(string $transport, string $event): void {
    $qb = $this->getEntityManager()->createQueryBuilder();

    $qb
      ->delete(MessengerTransportStatusEventSent::class, 'ses')
      ->andWhere($qb->expr()->eq('ses.transport', ':transport'))
      ->andWhere($qb->expr()->eq('ses.event', ':event'))
      ->setParameter('transport', $transport)
      ->setParameter('event', $event);

    $qb->getQuery()->execute();
  }

}
