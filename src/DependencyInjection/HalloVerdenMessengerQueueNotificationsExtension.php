<?php


namespace HalloVerden\MessengerQueueNotificationsBundle\DependencyInjection;


use HalloVerden\MessengerQueueEventsBundle\Event\MessageQueueEvent;
use Symfony\Component\Config\Definition\BaseNode;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * Class HalloVerdenMessengerQueueNotificationsExtension
 *
 * @package HalloVerden\MessengerQueueNotificationsBundle\DependencyInjection
 */
class HalloVerdenMessengerQueueNotificationsExtension extends ConfigurableExtension implements PrependExtensionInterface {

  /**
   * @inheritDoc
   * @throws \Exception
   */
  protected function loadInternal(array $mergedConfig, ContainerBuilder $container) {
    $loader = new YamlFileLoader($container, new FileLocator(__DIR__. '/../../config'));
    $loader->load('services.yaml');

    $container->getDefinition('hallo_verden_queue_notifications.listener.check_message_queue_stopped')
      ->setArgument('$configs', $mergedConfig['message_queue_stopped_event']['transports']);

    $container->getDefinition('hallo_verden_queue_notifications.listener.check_message_queue_has_messages')
      ->setArgument('$configs', $mergedConfig['message_queue_has_messages_event']['transports']);
  }

  /**
   * @inheritDoc
   */
  public function prepend(ContainerBuilder $container) {
    $configs = $container->getExtensionConfig($this->getAlias());
    $resolvingBag = $container->getParameterBag();

    // This is a internal method, but I find no other way of solving this.
    //   see https://github.com/symfony/symfony/issues/31608 + https://github.com/symfony/symfony/issues/40198
    BaseNode::setPlaceholderUniquePrefix($resolvingBag->getEnvPlaceholderUniquePrefix());

    $configs = $resolvingBag->resolveValue($configs);
    $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);

    $mapping = [];

    foreach (array_keys($config['message_queue_stopped_event']['transports']) as $transport) {
      $mapping[$transport] = [MessageQueueEvent::EVENT_INFORMATION_FIRST_AVAILABLE_MESSAGE, MessageQueueEvent::EVENT_INFORMATION_MESSAGE_COUNT];
    }

    foreach (array_keys($config['message_queue_has_messages_event']['transports']) as $transport) {
      $mapping[$transport] = \array_merge($mapping[$transport] ?? [], [MessageQueueEvent::EVENT_INFORMATION_MESSAGE_COUNT]);
    }

    $container->prependExtensionConfig('hallo_verden_messenger_queue_events', [
      'transport_event_information_mapping' => $mapping
    ]);
  }

}
