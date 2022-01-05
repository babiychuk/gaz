<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 24.08.2018
 * Time: 12:36
 */

namespace Drupal\uag_facets\Plugin\facets\widget;


use Drupal\facets\FacetInterface;
use Drupal\facets\Widget\WidgetPluginBase;

/**
 * Class PriceWidget
 *
 * @package Drupal\uag_facets\Plugin\facets\widget
 *
 * @FacetsWidget(
 *   id = "uag_price_widget",
 *   label = @Translation("Uag slider widget"),
 * )
 */
class PriceWidget extends WidgetPluginBase {
  public function build(FacetInterface $facet) {
    $results = $facet->getResults();
    ksort($results);
  }
}
