<?php

namespace HalloVerden\MessengerQueueNotificationsBundle\Command;

use Doctrine\DBAL\Exception;
use HalloVerden\MessengerQueueNotificationsBundle\Service\MessengerTransportStatusEventDispatcherService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'hallo_verden:messenger-transport-status-events:dispatch', description: 'Dispatches the needed messenger transport status events')]
final class DispatchMessengerTransportStatusEventsCommand extends Command {

  /**
   * DispatchMessengerTransportStatusEventsCommand constructor.
   */
  public function __construct(
    private readonly MessengerTransportStatusEventDispatcherService $eventDispatcherService
  ) {
    parent::__construct();
  }

  /**
   * @inheritDoc
   * @throws \DateInvalidOperationException|Exception
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->eventDispatcherService->dispatchTransportStatusEvents();
    return Command::SUCCESS;
  }

}
