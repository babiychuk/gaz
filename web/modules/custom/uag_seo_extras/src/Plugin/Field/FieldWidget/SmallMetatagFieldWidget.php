<?php

namespace Drupal\uag_seo_extras\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;
use Drupal\metatag\Plugin\Field\FieldWidget\MetatagFirehose;

/**
 * Plugin implementation of the 'small_metatag_field_widget' widget.
 *
 * @FieldWidget(
 *   id = "small_metatag_field_widget",
 *   label = @Translation("Small metatag field widget"),
 *   field_types = {
 *     "metatag"
 *   }
 * )
 */
class SmallMetatagFieldWidget extends MetatagFirehose {

  const ALLOWED_METATAGS = array(
    'basic' => array('title', 'description'),
    // 'advanced' => array(),
    'open_graph' => array('og_title', 'og_description', 'og_image'),
    'twitter_cards' => array('twitter_cards_description', 'twitter_cards_title', 'twitter_cards_image')
  );

  const OPENED_GROUPS = array(
    'basic',
  );
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $base = parent::formElement($items, $delta, $element, $form, $form_state);
    foreach (Element::children($base) as $tag_type_name) {
      if (!isset(self::ALLOWED_METATAGS[$tag_type_name])) {
        $base[$tag_type_name]['#access'] = FALSE;
        continue;
      }

      if (in_array($tag_type_name, self::OPENED_GROUPS)) {
        $base[$tag_type_name]['#open'] = true;
      }

      if (isset($base[$tag_type_name])) {
        foreach (Element::children($base[$tag_type_name]) as $tag_name) {
          if (!in_array($tag_name, self::ALLOWED_METATAGS[$tag_type_name])) {
            $base[$tag_type_name][$tag_name]['#access'] = FALSE;
          }
        }
      }
    }

    unset ($base['#group']);

    return $base;
  }
}
