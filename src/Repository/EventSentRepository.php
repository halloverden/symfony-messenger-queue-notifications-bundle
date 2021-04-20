<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\Repository;

use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;
use HalloVerden\MessengerQueueNotificationsBundle\Entity\EventSent;

/**
 * @method EventSent|null find($id, $lockMode = null, $lockVersion = null)
 * @method EventSent|null findOneBy(array $criteria, array $orderBy = null)
 * @method EventSent[]    findAll()
 * @method EventSent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventSentRepository extends ServiceEntityRepository implements EventSentRepositoryInterface {

  /**
   * EventSentRepository constructor.
   *
   * @param ManagerRegistry $registry
   */
  public function __construct(ManagerRegistry $registry) {
    parent::__construct($registry, EventSent::class);
  }

  /**
   * @inheritDoc
   * @throws ORMException
   */
  public function create(EventSent $eventSent): EventSent {
    $this->getEntityManager()->persist($eventSent);
    $this->getEntityManager()->flush();

    return $eventSent;
  }

  /**
   * @inheritDoc
   * @throws ORMException
   * @throws OptimisticLockException
   */
  public function updateLastSentNow(EventSent $eventSent): EventSent {
    $eventSent->setLastSent(new DateTime())->incrementCount();
    $this->getEntityManager()->flush();

    return $eventSent;
  }

  /**
   * @inheritDoc
   * @throws ORMException
   * @throws OptimisticLockException
   */
  public function delete(EventSent $eventSent): EventSent {
    $this->getEntityManager()->remove($eventSent);
    $this->getEntityManager()->flush();

    return $eventSent;
  }

  /**
   * @inheritDoc
   */
  public function getByEventAndTransport(string $event, string $transport): ?EventSent {
    return $this->findOneBy([
      'event' => $event,
      'transport' => $transport
    ]);
  }

}
