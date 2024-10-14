<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\MessengerTransportStatusRepository;

#[Entity(repositoryClass: MessengerTransportStatusRepository::class), Table(name: 'messenger_transport_status')]
class MessengerTransportStatus {

  #[Id, GeneratedValue(strategy: 'AUTO'), Column(name: 'id', type: Types::INTEGER, unique: true)]
  private readonly int $id;

  #[Column(name: 'transport', type: Types::STRING, unique: true, nullable: false)]
  private readonly string $transport;

  #[Column(name: 'last_available_message_count', type: Types::INTEGER, nullable: true)]
  private ?int $lastAvailableMessageCount = null;

  #[Column(name: 'last_worker_running_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastWorkerRunningAt = null;

  #[Column(name: 'last_worker_started_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastWorkerStartedAt = null;

  #[Column(name: 'last_worker_stopped_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastWorkerStoppedAt = null;

  #[Column(name: 'last_worker_rate_limited_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastWorkerRateLimitedAt = null;

  #[Column(name: 'last_worker_rate_limited_until', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastWorkerRateLimitedUntil = null;

  #[Column(name: 'last_message_sent_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastMessageSentAt = null;

  #[Column(name: 'last_message_received_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastMessageReceivedAt = null;

  #[Column(name: 'last_message_failed_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastMessageFailedAt = null;

  #[Column(name: 'last_message_handled_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
  private ?\DateTimeImmutable $lastMessageHandledAt = null;

  /**
   * MessengerTransportStatus constructor.
   */
  public function __construct(string $transport) {
    $this->transport = $transport;
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

  public function getLastAvailableMessageCount(): ?int {
    return $this->lastAvailableMessageCount;
  }

  public function setLastAvailableMessageCount(?int $lastAvailableMessageCount): self {
    $this->lastAvailableMessageCount = $lastAvailableMessageCount;
    return $this;
  }

  public function getLastWorkerRunningAt(): ?\DateTimeImmutable {
    return $this->lastWorkerRunningAt;
  }

  public function setLastWorkerRunningAt(?\DateTimeImmutable $lastWorkerRunningAt): self {
    $this->lastWorkerRunningAt = $lastWorkerRunningAt;
    return $this;
  }

  public function getLastWorkerStartedAt(): ?\DateTimeImmutable {
    return $this->lastWorkerStartedAt;
  }

  public function setLastWorkerStartedAt(?\DateTimeImmutable $lastWorkerStartedAt): self {
    $this->lastWorkerStartedAt = $lastWorkerStartedAt;
    return $this;
  }

  public function getLastWorkerStoppedAt(): ?\DateTimeImmutable {
    return $this->lastWorkerStoppedAt;
  }

  public function setLastWorkerStoppedAt(?\DateTimeImmutable $lastWorkerStoppedAt): self {
    $this->lastWorkerStoppedAt = $lastWorkerStoppedAt;
    return $this;
  }

  public function getLastWorkerRateLimitedAt(): ?\DateTimeImmutable {
    return $this->lastWorkerRateLimitedAt;
  }

  public function setLastWorkerRateLimitedAt(?\DateTimeImmutable $lastWorkerRateLimitedAt): self {
    $this->lastWorkerRateLimitedAt = $lastWorkerRateLimitedAt;
    return $this;
  }

  public function getLastWorkerRateLimitedUntil(): ?\DateTimeImmutable {
    return $this->lastWorkerRateLimitedUntil;
  }

  public function setLastWorkerRateLimitedUntil(?\DateTimeImmutable $lastWorkerRateLimitedUntil): static {
    $this->lastWorkerRateLimitedUntil = $lastWorkerRateLimitedUntil;
    return $this;
  }

  public function getLastMessageSentAt(): ?\DateTimeImmutable {
    return $this->lastMessageSentAt;
  }

  public function setLastMessageSentAt(?\DateTimeImmutable $lastMessageSentAt): self {
    $this->lastMessageSentAt = $lastMessageSentAt;
    return $this;
  }

  public function getLastMessageReceivedAt(): ?\DateTimeImmutable {
    return $this->lastMessageReceivedAt;
  }

  public function setLastMessageReceivedAt(?\DateTimeImmutable $lastMessageReceivedAt): self {
    $this->lastMessageReceivedAt = $lastMessageReceivedAt;
    return $this;
  }

  public function getLastMessageFailedAt(): ?\DateTimeImmutable {
    return $this->lastMessageFailedAt;
  }

  public function setLastMessageFailedAt(?\DateTimeImmutable $lastMessageFailedAt): self {
    $this->lastMessageFailedAt = $lastMessageFailedAt;
    return $this;
  }

  public function getLastMessageHandledAt(): ?\DateTimeImmutable {
    return $this->lastMessageHandledAt;
  }

  public function setLastMessageHandledAt(?\DateTimeImmutable $lastMessageHandledAt): self {
    $this->lastMessageHandledAt = $lastMessageHandledAt;
    return $this;
  }

}
