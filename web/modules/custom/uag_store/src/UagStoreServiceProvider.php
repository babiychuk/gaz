<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 12.09.2018
 * Time: 12:50
 */

namespace Drupal\uag_store;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class UagStoreServiceProvider extends ServiceProviderBase {
  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('menu.active_trail');

    // Use CustomMenuActiveTrail class instead of the
    // default MenuActiveTrail class.
    $definition->setClass(MenuActiveLink::class);
  }
}
