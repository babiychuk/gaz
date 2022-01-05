<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 25.05.2018
 * Time: 11:34
 */

namespace Drupal\gaz_migrate\Plugin\migrate\process;

use Drupal\Component\Utility\Html;
use Drupal\Core\Url;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\TransferStats;

/**
 *
 * @MigrateProcessPlugin(
 *   id = "gaz_process_get_inline_images"
 * )
 */
class GetInlineImages extends ProcessPluginBase {
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    if (!isset($this->configuration['dest_dir'])) {
      $this->configuration['dest_dir'] = 'public://inline-images/';
    }

    if (!isset($this->configuration['base_url'])) {
      throw new \Exception('no base url');
    }
  }

  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Get all images at body.
    $dom = Html::load(html_entity_decode($value));
    $images = $dom->getElementsByTagName('img');
    $base_url = $this->configuration['base_url'];


    $search = [];
    $replace = [];

    // $destination = '' . $this->getImageUrlDateData($src);

    foreach ($images as $img) {
      /** @var \DOMElement $img */
      $src = $img->getAttribute('src');
      $original_src = $src;

      if (!filter_var($src, FILTER_VALIDATE_URL, FILTER_FLAG_SCHEME_REQUIRED)) {
        $src = $base_url . $src;
      }

      $destination = $this->getDest($src);
      if (file_prepare_directory($destination, FILE_CREATE_DIRECTORY & FILE_MODIFY_PERMISSIONS)) {
        try {
          $client = new Client(['base_url'=> $base_url]);
          $response = $client->get($src);
          $status = $response->getStatusCode();
          if ($status == 200) {
            $file = system_retrieve_file($src, $destination, TRUE, FILE_EXISTS_REPLACE);
            if ($file !== FALSE) {
              /** @var \Drupal\file\FileInterface $file */
              $img->setAttribute('data-entity-type', 'file');
              $img->setAttribute('data-entity-uuid', $file->uuid());
              $url = file_url_transform_relative(file_create_url($file->getFileUri()));
              // $url = Url::fromUri($file->getFileUri())->get;
              $img->setAttribute('src', $url);
              $search[] = "#(<img.*src=.*" . $original_src . "[^>]*>)#i";
              $replace[] = $dom->saveHTML($img);
            }
          }
        } catch (RequestException $exception) {
          dump($exception->getMessage());
          dump($row);
        }
      } else {
        throw new \Exception('Dir problem: ' . $destination);
      }
    }

    $value = preg_replace($search, $replace, $value);
    return $value;
  }

  public function getDest($src) {
    $dest_dir = $this->configuration['dest_dir'];
    return $dest_dir;
  }
}
