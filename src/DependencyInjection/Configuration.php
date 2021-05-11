<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface {

  /**
   * @inheritDoc
   */
  public function getConfigTreeBuilder(): TreeBuilder {
    $treeBuilder = new TreeBuilder('hallo_verden_messenger_queue_notifications');

    $treeBuilder->getRootNode()
      ->children()
        ->arrayNode('message_queue_stopped_event')
          ->addDefaultsIfNotSet()
          ->children()
            ->arrayNode('transports')
              ->arrayPrototype()
                ->children()
                  ->integerNode('max_handle_time')->defaultValue(300)->end()
                  ->integerNode('event_interval')->defaultValue(1800)->end()
                  ->integerNode('max_events')->defaultValue(100)->end()
                ->end()
              ->end()
            ->end()
          ->end()
        ->end()
      ->end();

    $treeBuilder->getRootNode()
      ->children()
        ->arrayNode('message_queue_has_messages_event')
          ->addDefaultsIfNotSet()
          ->children()
            ->arrayNode('transports')
              ->arrayPrototype()
                ->children()
                  ->integerNode('max_messages')->defaultValue(0)->end()
                  ->integerNode('event_interval')->defaultValue(1800)->end()
                  ->integerNode('max_events')->defaultValue(100)->end()
                ->end()
              ->end()
            ->end()
          ->end()
        ->end()
      ->end();

    return $treeBuilder;
  }

}
