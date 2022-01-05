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
 *  id = "gaz_migrate_content"
 * )
 */
class GazMigrateContent extends GazMigrateContentBase {
  /**
   * {@inheritdoc}
   */
  public function query() {
    $query = parent::query();

    $query->condition('context_key', 'web');
    $query->condition('isfolder', 0);
    $query->condition('published', 1);
    $uri_cond = $query->orConditionGroup();
    $uri_cond->condition('uri', 'informacziya/%', 'LIKE');
    $uri_cond->condition('uri', 'menyu/%', 'LIKE');
    $uri_cond->condition('uri', 'o-kompanii-menu/%', 'LIKE');
    $uri_cond->condition('uri', 'sxemyi-gazosnabzheniya/%', 'LIKE');
    $query->condition($uri_cond);
    // $query->condition('alias', $skip_aliases, 'NOT IN');
    // $query->condition('uri', 'catalog/%', 'LIKE');

    $query->fields('p');
    return $query;
  }
}
