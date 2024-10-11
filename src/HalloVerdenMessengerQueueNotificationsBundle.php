<?php


namespace HalloVerden\MessengerQueueNotificationsBundle;


use HalloVerden\MessengerQueueNotificationsBundle\Command\DispatchMessengerTransportStatusEventsCommand;
use HalloVerden\MessengerQueueNotificationsBundle\Config\MessengerTransportHasMessagesConfig;
use HalloVerden\MessengerQueueNotificationsBundle\Config\MessengerTransportHasStoppedConfig;
use HalloVerden\MessengerQueueNotificationsBundle\EventListener\UpdateMessengerTransportStatusListener;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\MessengerTransportStatusEventSentRepository;
use HalloVerden\MessengerQueueNotificationsBundle\Repository\MessengerTransportStatusRepository;
use HalloVerden\MessengerQueueNotificationsBundle\Service\MessengerTransportInfoService;
use HalloVerden\MessengerQueueNotificationsBundle\Service\MessengerTransportStatusEventDispatcherService;
use HalloVerden\MessengerQueueNotificationsBundle\Service\MessengerTransportStatusService;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;


class HalloVerdenMessengerQueueNotificationsBundle extends AbstractBundle {

  /**
   * @inheritDoc
   */
  public function configure(DefinitionConfigurator $definition): void {
    $definition->rootNode()
      ->addDefaultsIfNotSet()
      ->children()
        ->arrayNode('messenger_transport_has_messages_event')
          ->useAttributeAsKey('transport')
          ->arrayPrototype()
            ->addDefaultsIfNotSet()
            ->children()
              ->arrayNode('event_intervals')
                ->beforeNormalization()->castToArray()->end()
                ->defaultValue(['1 hour'])
                ->requiresAtLeastOneElement()
                ->scalarPrototype()->end()
              ->end()
              ->integerNode('max_messages')->defaultValue(0)->end()
            ->end()
          ->end()
        ->end()
        ->arrayNode('messenger_transport_has_stopped_event')
          ->useAttributeAsKey('transport')
          ->arrayPrototype()
            ->addDefaultsIfNotSet()
            ->children()
              ->arrayNode('event_intervals')
                ->beforeNormalization()->castToArray()->end()
                ->defaultValue(['1 hour'])
                ->requiresAtLeastOneElement()
                ->scalarPrototype()->end()
              ->end()
              ->scalarNode('max_worker_running_interval')->defaultValue('5 minutes')->end()
            ->end()
          ->end()
        ->end()
      ->end()
    ;
  }

  /**
   * @inheritDoc
   */
  public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void {
    $alias = $this->getContainerExtension()->getAlias();

    $eventConfigs = [];
    foreach ($config['messenger_transport_has_messages_event'] as $transport => $hasMessagesConfigArray) {
      $eventConfigs[] = $builder->register($alias . 'config.has_messages.' . $transport, MessengerTransportHasMessagesConfig::class)
        ->setFactory([MessengerTransportHasMessagesConfig::class, 'create'])
        ->setArguments([$hasMessagesConfigArray['event_intervals'], $transport, $hasMessagesConfigArray['max_messages']]);
    }

    foreach ($config['messenger_transport_has_stopped_event'] as $transport => $hasStoppedConfigArray) {
      $eventConfigs[] = $builder->register($alias . 'config.has_stopped.' . $transport, MessengerTransportHasStoppedConfig::class)
        ->setFactory([MessengerTransportHasStoppedConfig::class, 'create'])
        ->setArguments([$hasStoppedConfigArray['event_intervals'], $transport, $hasStoppedConfigArray['max_worker_running_interval']]);
    }

    $messengerTransportInfoServiceId = $alias . '.messenger_transport_info_service';
    $messengerTransportStatusRepositoryId = $alias . '.messenger_transport_status_repository';
    $messengerTransportStatusServiceId = $alias . '.messenger_transport_status_service';
    $messengerTransportStatusEventSentRepositoryId = $alias . '.messenger_transport_status_event_sent_repository';
    $messengerTransportStatusEventDispatcherServiceId = $alias . '.messenger_transport_status_event_dispatcher_service';
    $updateMessengerTransportStatusListenerId = $alias . '.update_messenger_transport_status_listener';
    $dispatchMessengerTransportStatusEventsCommandId = $alias . '.dispatch_messenger_transport_status_events_command';

    $container->services()
      ->set($messengerTransportInfoServiceId , MessengerTransportInfoService::class)
        ->args([service('messenger.receiver_locator')])
      ->set($messengerTransportStatusRepositoryId, MessengerTransportStatusRepository::class)
        ->args([service('doctrine')])
      ->set($messengerTransportStatusEventSentRepositoryId, MessengerTransportStatusEventSentRepository::class)
        ->args([service('doctrine')])
      ->set($messengerTransportStatusServiceId, MessengerTransportStatusService::class)
        ->args([service($messengerTransportStatusRepositoryId), service('lock.factory'), service('doctrine')])
      ->set($messengerTransportStatusEventDispatcherServiceId, MessengerTransportStatusEventDispatcherService::class)
        ->args([
          $eventConfigs,
          service($messengerTransportStatusServiceId),
          service($messengerTransportInfoServiceId),
          service($messengerTransportStatusEventSentRepositoryId),
          service('event_dispatcher'),
          service('clock'),
          service('doctrine'),
          service('logger')->nullOnInvalid()
        ])
      ->set($updateMessengerTransportStatusListenerId, UpdateMessengerTransportStatusListener::class)
        ->args([service($messengerTransportStatusServiceId), service($messengerTransportInfoServiceId), service('clock'), service('doctrine')])
        ->tag('kernel.event_subscriber')
      ->set($dispatchMessengerTransportStatusEventsCommandId, DispatchMessengerTransportStatusEventsCommand::class)
        ->args([service($messengerTransportStatusEventDispatcherServiceId)])
        ->tag('console.command')
    ;
  }

}
