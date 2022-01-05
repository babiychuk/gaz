<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 24.05.2018
 * Time: 18:44
 */

namespace Drupal\gaz_migrate\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Class BuildMetatag
 *
 * @package Drupal\gaz_migrate\Plugin\migrate\process
 *
 * * @MigrateProcessPlugin(
 *   id = "gaz_process_build_metatag",
 *   handle_multiples = TRUE
 * )
 */
class BuildMetatag extends ProcessPluginBase {
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $value = serialize($value);
    return $value;
  }
}
