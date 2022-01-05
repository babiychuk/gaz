<?php

namespace Drupal\uag_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ObjectsMapBlock' block.
 *
 * @Block(
 *  id = "uag_objects_map_block",
 *  admin_label = @Translation("Objects map block"),
 * )
 */
class ObjectsMapBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['uag_objects_map_block'] = [
      '#theme' => 'uag_block_objects_map',
    ];
    $build['#attached']['library'][] = 'uag_blocks/block-objects-map';
    $build['#attached']['drupalSettings']['uag_objects_map_block']['render_regions'] = $this->render_regions();

    return $build;
  }

  protected function regions() {
    $regions = [
      "07" => [
        '#title' => $this->t('Volyn Oblast'),
        '#objects' => 0,
      ],
      "56" => [
        '#title' => $this->t('Rivne Oblast'),
        '#objects' => 2,
      ],
      "18" => [
        '#title' => $this->t('Zhytomyr Oblast'),
        '#objects' => 5,
      ],
      "32" => [
        '#title' => $this->t('Kiev Oblast'),
        '#objects' => 12,
      ],
      "74" => [
        '#title' => $this->t('Chernihiv Oblast'),
        '#objects' => 9,
      ],
      "59" => [
        '#title' => $this->t("Sumy Oblast"),
        '#objects' => 6,
      ],
      "46" => [
        '#title' => $this->t("Lviv Oblast"),
        '#objects' => 1,
      ],
      "61" => [
        '#title' => $this->t("Ternopil Oblast"),
        '#objects' => 0,
      ],
      "68" => [
        '#title' => $this->t("Khmelnytskyi Oblast"),
        '#objects' => 1,
      ],
      "05" => [
        '#title' => $this->t("Vinnytsia Oblast"),
        '#objects' => 13,
      ],
      "71" => [
        '#title' => $this->t("Cherkasy Oblast"),
        '#objects' => 8,
      ],
      "53" => [
        '#title' => $this->t("Poltava Oblast"),
        '#objects' => 1,
      ],
      "63" => [
        '#title' => $this->t("Kharkiv Oblast"),
        '#objects' => 6,
      ],
      "09" => [
        '#title' => $this->t("Luhansk Oblast"),
        '#objects' => 11,
      ],
      "21" => [
        '#title' => $this->t("Zakarpattia Oblast"),
        '#objects' => 0,
      ],
      "26" => [
        '#title' => $this->t("Ivano-Frankivsk Oblast"),
        '#objects' => 0,
      ],
      "77" => [
        '#title' => $this->t("Chernivtsi Oblast"),
        '#objects' => 0,
      ],
      "35" => [
        '#title' => $this->t("Kirovohrad Oblast"),
        '#objects' => 14,
      ],
      "12" => [
        '#title' => $this->t("Dnipropetrovsk Oblast"),
        '#objects' => 3,
      ],
      "14" => [
        '#title' => $this->t("Donetsk Oblast"),
        '#objects' => 0,
      ],
      "51" => [
        '#title' => $this->t("Odessa Oblast"),
        '#objects' => 0,
      ],
      "48" => [
        '#title' => $this->t("Mykolaiv Oblast"),
        '#objects' => 1,
      ],
      "65" => [
        '#title' => $this->t("Kherson Oblast"),
        '#objects' => 0,
      ],
      "23" => [
        '#title' => $this->t("Zaporizhia Oblast"),
        '#objects' => 0,
      ],
      "43" => [
        '#title' => $this->t("AR Crimea"),
        '#objects' => 0,
      ],
    ];

    foreach ($regions as $code => $region) {
      $regions[$code]['#theme'] = 'uag_block_objects_map_region';
      $regions[$code]['#phone'] = '(067) 433-88-70';
    }

    return $regions;
  }

  /**
   * @throws \Exception
   */
  protected function render_regions() {
    $regions = $this->regions();
    /** @var \Drupal\Core\Render\RendererInterface $renderer */
    $renderer = \Drupal::service('renderer');

    $render = array();
    foreach ($regions as $code => $region) {
      $render[$code] = $renderer->render($region);
    }
    return $render;
  }
}
