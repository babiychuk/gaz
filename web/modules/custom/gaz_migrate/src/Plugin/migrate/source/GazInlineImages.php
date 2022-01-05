<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 25.05.2018
 * Time: 13:01
 */

namespace Drupal\gaz_migrate\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

class GazInlineImages extends SqlBase {

  public function query() {

  }

  /**
   * Returns available fields on the source.
   *
   * @return array
   *   Available fields in the source, keys are the field machine names as used
   *   in field mappings, values are descriptions.
   */
  public function fields() {
    return array();
  }

  public function getIds() {
    // TODO: Implement getIds() method.
    return array(
      'src' => array(
        'type' => 'string',
      )
    );
  }

  public function prepareRow(Row $row) {
    return parent::prepareRow($row);
  }
}
