<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 12.09.2018
 * Time: 12:47
 */

namespace Drupal\uag_store;

use Drupal\Core\Entity\EntityMalformedException;
use Drupal\Core\Menu\MenuActiveTrail;

class MenuActiveLink extends MenuActiveTrail {
  /**
   * {@inheritdoc}
   */
  public function getActiveLink($menu_name = NULL) {
    $config = \Drupal::config('uag_store.store_settings');
    // Call the parent method to implement the default behavior.
    $found = parent::getActiveLink($menu_name);

    if ($this->routeMatch->getRouteName() == 'entity.commerce_product.canonical' &&
      $product = $this->routeMatch->getParameter('commerce_product')) {

      /** @var \Drupal\commerce_product\Entity\ProductInterface $product */
      if ($product->hasField('field_category')) {
        $field_category = $product->get('field_category');
        foreach ($field_category as $category) {
          /** @var \Drupal\taxonomy\TermInterface $category */
          try {
            $route = $category->entity->toUrl();
            $links = $this->menuLinkManager->loadLinksByRoute($route->getRouteName(), $route->getRouteParameters(), $menu_name);

            if ($links) {
              $found = reset($links);
            }
          } catch (EntityMalformedException $e) {
            continue;
          }
        }
      }
    }

    return $found;
  }
}
