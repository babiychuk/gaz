<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 24.05.2018
 * Time: 14:02
 */

namespace Drupal\gaz_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "gaz_process_filter_number"
 * )
 */
class FilterNumber extends ProcessPluginBase {
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value = preg_replace("/[^0-9,.]/", "", $value);
    $value = preg_replace('/\s+/', '', $value);
    $value = (float) $value;

    return $value;
  }

}
