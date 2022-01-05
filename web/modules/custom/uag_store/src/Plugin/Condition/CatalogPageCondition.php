<?php

namespace Drupal\uag_store\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\Context\ContextDefinition;
use Drupal\node\NodeStorageInterface;
use Drupal\taxonomy\TermStorageInterface;
use Psr\Container\ContainerInterface;

/**
* Provides a 'Catalog page condition' condition to enable a condition based in module selected status.
*
* @Condition(
*   id = "catalog_page_condition",
*   label = @Translation("Catalog page condition"),
*   context = {
*     "node" = @ContextDefinition("entity:node", required = FALSE , label = @Translation("node")),
*     "term" = @ContextDefinition("entity:taxonomy_term", required = FALSE , label = @Translation("taxonomy term")),
 *    "product" = @ContextDefinition("entity:commerce_product", required = FALSE , label = @Translation("Commerce product")),
*   }
* )
*
*/
class CatalogPageCondition extends ConditionPluginBase {
/**
* {@inheritdoc}
*/
public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
{
  return new static(
    $configuration,
    $plugin_id,
    $plugin_definition
  );
}

  /**
   * Creates a new CatalogPageCondition object.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
 public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
 }

 /**
   * {@inheritdoc}
   */
 public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
     $form['catalog_root'] = [
         '#type' => 'select',
         '#title' => $this->t('Is catalog root'),
         '#default_value' => $this->configuration['catalog_root'],
         '#options' => [
           'any' => $this->t('Any'),
           'yes' => $this->t('Yes'),
           'no' => $this->t('No'),
         ],
     ];

   $form['catalog_category'] = [
     '#type' => 'select',
     '#title' => $this->t('Is catalog category page'),
     '#default_value' => $this->configuration['catalog_category'],
     '#options' => [
       'any' => $this->t('Any'),
       'yes' => $this->t('Yes'),
       'no' => $this->t('No'),
     ],
   ];
   $form['catalog_product'] = [
     '#type' => 'select',
     '#title' => $this->t('Is catalog product page'),
     '#default_value' => $this->configuration['catalog_product'],
     '#options' => [
       'any' => $this->t('Any'),
       'yes' => $this->t('Yes'),
       'no' => $this->t('No'),
     ],
   ];

   return parent::buildConfigurationForm($form, $form_state);
 }

/**
 * {@inheritdoc}
 */
 public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
   $this->configuration['catalog_root'] = $form_state->getValue('catalog_root');
   $this->configuration['catalog_category'] = $form_state->getValue('catalog_category');
   $this->configuration['catalog_product'] = $form_state->getValue('catalog_product');

   parent::submitConfigurationForm($form, $form_state);
 }

/**
 * {@inheritdoc}
 */
 public function defaultConfiguration() {
    return [
      'catalog_root' => 'any',
      'catalog_category' => 'any',
      'catalog_product' => 'any',
    ] + parent::defaultConfiguration();
 }

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function evaluate() {
    $config = $config = \Drupal::config('uag_store.store_settings');
    // $em = \Drupal::entityTypeManager();

    // $vocab = $em->getStorage('taxonomy_vocabulary')
    //   ->load($config->get('catalog_vocabulary'));

    // $node = $em->getStorage('node')
    //   ->load($config->get('root_catalog_page'));

    if (($node = $this->getContextValue('node')) && $this->configuration['catalog_root'] != 'any') {
      /** @var \Drupal\node\NodeInterface $node */

      if ($this->configuration['catalog_root'] == 'yes') {
        if ($node->id() == $config->get('root_catalog_page')) {
          return TRUE;
        }
      }
      // else {
      //   if ($node->id() != $config->get('root_catalog_page')) {
      //     return true;
      //   }
      // }
    }

    if (($term = $this->getContextValue('term')) && $this->configuration['catalog_category'] != 'any') {
      /** @var \Drupal\taxonomy\TermInterface $term */

      if ($this->configuration['catalog_category'] == 'yes') {
        if ($term->bundle() == $config->get('catalog_vocabulary')) {
          return TRUE;
        }
      }
      // else {
      //   if ($term->bundle() != $config->get('catalog_vocabulary')) {
      //     return TRUE;
      //   }
      // }
    }

    if (($product = $this->getContextValue('product')) && $this->configuration['catalog_product'] != 'any') {
      /** @var \Drupal\commerce_product\Entity\ProductInterface $product */

      if ($this->configuration['catalog_product'] == 'yes') {
        return TRUE;
      } else {
        return FALSE;
      }
    }
    return FALSE;
  }

/**
 * Provides a human readable summary of the condition's configuration.
 */
 public function summary()
 {
     $module = $this->getContextValue('module');
     $modules = system_rebuild_module_data();

     $status = ($modules[$module]->status)?t('enabled'):t('disabled');

     return t('The module @module is @status.', ['@module' => $module, '@status' => $status]);
 }

}
