<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table(name: 'messenger_transport_status_event_sent')]
class MessengerTransportStatusEventSent {

  #[Id, GeneratedValue(strategy: 'AUTO'), Column(name: 'id', type: Types::INTEGER, unique: true)]
  private readonly int $id;

  #[Column(name: 'transport', type: Types::STRING, nullable: false)]
  private readonly string $transport;

  #[Column(name: 'event', type: Types::STRING, nullable: false)]
  private readonly string $event;

  #[Column(name: 'sent_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
  private readonly \DateTimeImmutable $sentAt;

  /**
   * MessengerTransportStatusEventSent constructor.
   */
  public function __construct(string $transport, string $event, \DateTimeImmutable $sentAt) {
    $this->transport = $transport;
    $this->event = $event;
    $this->sentAt = $sentAt;
  }

  public function getId(): int {
    return $this->id;
  }

  public function setId(int $id): self {
    $this->id = $id;
    return $this;
  }

  public function getTransport(): string {
    return $this->transport;
  }

  public function getEvent(): string {
    return $this->event;
  }

  public function getSentAt(): \DateTimeImmutable {
    return $this->sentAt;
  }

}
