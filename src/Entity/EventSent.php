<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class EventSent {

  #[ORM\Id, ORM\GeneratedValue(strategy: 'AUTO'), ORM\Column(name: 'id', type: 'integer', unique: true)]
  private int $id;

  #[ORM\Column(name: 'event', type: 'string', nullable: false)]
  private string $event;

  #[ORM\Column(name: 'count', type: 'integer', nullable: false)]
  private int $count;

  #[ORM\Column(name: 'transport', type: 'string', nullable: false)]
  private string $transport;

  #[ORM\Column(name: 'last_sent', type: 'datetime', nullable: false)]
  private \DateTime $lastSent;

  /**
   * EventSent constructor.
   */
  public function __construct(string $event, string $transport) {
    $this->event = $event;
    $this->transport = $transport;
    $this->count = 1;
    $this->lastSent = new \DateTime();
  }

  /**
   * @return int
   */
  public function getId(): int {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getEvent(): string {
    return $this->event;
  }

  /**
   * @return int
   */
  public function getCount(): int {
    return $this->count;
  }

  /**
   * @return EventSent
   */
  public function incrementCount(): self {
    $this->count++;
    return $this;
  }

  /**
   * @return string
   */
  public function getTransport(): string {
    return $this->transport;
  }

  /**
   * @return \DateTime
   */
  public function getLastSent(): \DateTime {
    return $this->lastSent;
  }

  /**
   * @param \DateTime $lastSent
   *
   * @return EventSent
   */
  public function setLastSent(\DateTime $lastSent): self {
    $this->lastSent = $lastSent;
    return $this;
  }

}
