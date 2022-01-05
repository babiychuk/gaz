<?php

namespace Drupal\gaz_migrate\Plugin\migrate\source;

use Drupal\Core\State\StateInterface;
use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;

/**
 * Provides a 'GazMigrateUsers' migrate source.
 *
 * @MigrateSource(
 *  id = "gaz_migrate_products"
 * )
 */
class GazMigrateProducts extends GazMigrateProductVariations {

  /**
   * @var \Drupal\commerce_store\Entity\Store
   */
  protected $defaultStore;

  /**
   * GazMigrateProducts constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   * @param \Drupal\Core\State\StateInterface $state
   *
   * @throws \Drupal\migrate\MigrateException
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, \Drupal\migrate\Plugin\MigrationInterface $migration, \Drupal\Core\State\StateInterface $state) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration, $state);

    $this->defaultStore = \Drupal::service('commerce_store.default_store_resolver')->resolve();
    if (!$this->defaultStore) {
      throw new MigrateException('You must have a store saved in order to import products.');
    }
  }

  function prepareRow(Row $row) {
    $ret = parent::prepareRow($row);

    $sku = $row->getSourceProperty('id');
    $query = \Drupal::entityQuery('commerce_product_variation')
      ->condition('sku', $sku);

    $values = $query->execute();
    foreach ($values as $value) {
      $targets[] = ['target_id' => $value];
    }
    $row->setDestinationProperty('variations', $targets);
    $row->rehash();

    $row->setDestinationProperty('stores', [
      'target_id' => $this->defaultStore->id(),
    ]);

    return $ret;
  }
}
