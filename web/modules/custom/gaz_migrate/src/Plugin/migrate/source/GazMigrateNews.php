<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 24.05.2018
 * Time: 11:07
 */

namespace Drupal\gaz_migrate\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Provides a 'GazMigrateCatalogStructure' migrate source.
 *
 * @MigrateSource(
 *  id = "gaz_migrate_news"
 * )
 */
class GazMigrateNews extends GazMigrateContentBase {
  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();

    $query->condition('uri', 'novosti/%', 'LIKE');

    return $query;
  }
}
