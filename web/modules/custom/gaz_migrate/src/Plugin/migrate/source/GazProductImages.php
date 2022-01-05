<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 28.06.2018
 * Time: 15:02
 */

namespace Drupal\gaz_migrate\Plugin\migrate\source;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Row;


/**
 * Provides a 'GazProductImages' migrate source.
 *
 * @MigrateSource(
 *  id = "gaz_migrate_product_images"
 * )
 */
class GazProductImages extends GazMigrateProductVariations {
  public function query() {
    $query = parent::query();
    $query->isNotNull('var_image.value');

    return $query;
  }

  /**
   * @param \Drupal\migrate\Row $row
   *
   * @return bool
   * @throws \Exception
   */
  public function prepareRow(Row $row) {
    $ret = parent::prepareRow($row);

    if (!$ret) {
      return $ret;
    }

    $row->setSourceProperty('image_filename', basename($row->getSourceProperty('var_image')));

    return TRUE;
  }
}
