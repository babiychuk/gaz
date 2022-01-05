<?php

namespace Drupal\uag_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;

/**
 * Provides a 'CalculatorBlock' block.
 *
 * @Block(
 *  id = "calculator_block",
 *  admin_label = @Translation("Calculator block"),
 * )
 */
class CalculatorBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $base_path = \Drupal::moduleHandler()->getModule('uag_blocks')->getPath() . '/images/';

    $build['calculator_block'] = [
      '#theme' => 'uag_block_calculator',
      '#sliders' => array(
        'lpg' => array(
          'title' => $this->t('LPG'),
          'img' => $base_path . 'icon-calc-lpg.png',
          'value' => 22.9,
        ),
        'gaz' => array(
          'title' => $this->t('Natural gas'),
          'img' => $base_path . 'icon-calc-gaz.png',
          'value' => 12.9,
        ),
        'diesel' => array(
          'title' => $this->t('Diesel'),
          'img' => $base_path . 'icon-calc-diesel.png',
          'value' => 25,
        ),
        'electricity' => array(
          'title' => $this->t('Electricity'),
          'img' => $base_path . 'icon-calc-electricity.png',
          'value' => 2.44,
        ),
        'pellets' => array(
          'title' => $this->t('Pellets'),
          'img' => $base_path . 'icon-calc-pellets.png',
          'value' => 3.5,
        )
      ),
      /*
      '#table_rows' => array(
        'Теплотворность (кВт)',
        'Теплотворность (ККал)',
        'КПД котла',
        'Максимальный расход в час*',
        'Номинальный расход в сутки',
        'Номинальный расход в месяц',
        'Номинальный расход в год *'
      ),
      */
      '#table_rows' => array(
        $this->t('Calorific value (kW)'),
        $this->t('Calorific value (Kcal)'),
        $this->t('Boiler efficiency'),
        $this->t('Maximum consumption per hour *'),
        $this->t('Nominal consumption per day'),
        $this->t('Nominal consumption per month'),
        $this->t('Nominal consumption per year *')
      ),
      '#boiler_power' => 3500,
    ];

    $build['#attached']['library'][] = 'uag_blocks/block-calculator';
    $settings = [
      'lpg' => array(
        'ttw' => 12.8,
        'kpd' => 0.92,
      ),
      'gaz' => array(
        'ttw' => 9.2,
        'kpd' => 0.92,
      ),
      'diesel' => array(
        'ttw' => 11.9,
        'kpd' => 0.85,
      ),
      'electricity' => array(
        'ttw' => 1,
        'kpd' => 0.98,
      ),
      'pellets' => array(
        'ttw' => 4.5,
        'kpd' => 0.85,
      )
    ];

    $build['#attached']['drupalSettings']['calculator_block']['storage_types'] = $settings;

    return $build;
  }

}
