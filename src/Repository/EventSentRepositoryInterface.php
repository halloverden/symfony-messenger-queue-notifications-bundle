<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\Repository;

use HalloVerden\MessengerQueueNotificationsBundle\Entity\EventSent;

/**
 * Interface EventSentRepositoryInterface
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\Repository
 */
interface EventSentRepositoryInterface {

  /**
   * @param EventSent $eventSent
   *
   * @return EventSent
   */
  public function create(EventSent $eventSent): EventSent;

  /**
   * @param EventSent $eventSent
   *
   * @return EventSent
   */
  public function updateLastSentNow(EventSent $eventSent): EventSent;

  /**
   * @param EventSent $eventSent
   *
   * @return EventSent
   */
  public function delete(EventSent $eventSent): EventSent;

  /**
   * @param string $event
   * @param string $transport
   *
   * @return EventSent|null
   */
  public function getByEventAndTransport(string $event, string $transport): ?EventSent;

}
