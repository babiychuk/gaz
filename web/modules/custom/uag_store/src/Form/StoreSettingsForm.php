<?php

namespace Drupal\uag_store\Form;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;
use Drupal\taxonomy\VocabularyStorage;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class StoreSettingsForm.
 */
class StoreSettingsForm extends ConfigFormBase {
  /** @var \Drupal\taxonomy\VocabularyStorage  */
  protected $vocabularyStorage;

  /** @var ConfigEntityStorageInterface */
  protected $menuStorage;

  /**
   * StoreSettingsForm constructor.
   *
   * @param \Drupal\taxonomy\VocabularyStorage $vocabularyStorage
   * @param \Drupal\Core\Config\Entity\ConfigEntityStorageInterface $menu_storage
   */
  public function __construct(VocabularyStorage $vocabularyStorage, ConfigEntityStorageInterface $menu_storage) {
    $this->vocabularyStorage = $vocabularyStorage;
    $this->menuStorage = $menu_storage;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')->getStorage('taxonomy_vocabulary'),
      $container->get('entity_type.manager')->getStorage('menu')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'uag_store.store_settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'store_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('uag_store.store_settings');

    $node = Node::load($config->get('root_catalog_page'));
    $form['root_catalog_page'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Root catalog page'),
      '#default_value' => $node,
      '#target_type' => 'node',
    ];

    $vocab_options = [];
    foreach ($this->vocabularyStorage->loadMultiple() as $vocabulary) {
      $vocab_options[$vocabulary->id()] = $vocabulary->label();
    }
    $form['catalog_vocabulary'] = [
      '#type' => 'select',
      '#title' => $this->t('Catalog vocabulary'),
      '#default_value' => $config->get('catalog_vocabulary'),
      '#options' => $vocab_options
    ];

    $menu_options = [];
    foreach ($this->menuStorage->loadMultiple() as $key => $menu) {
      /** @var \Drupal\system\Entity\Menu $menu */
      $menu_options[$key] = $menu->label();
    }

    /* @deprecated catalog_menu */
    $form['catalog_menu'] = [
      '#type' => 'select',
      '#title' => $this->t('Catalog menu name'),
      '#default_value' => $config->get('catalog_menu'),
      '#options' => $menu_options
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('uag_store.store_settings')
      ->set('root_catalog_page', $form_state->getValue('root_catalog_page'))
      ->set('catalog_vocabulary', $form_state->getValue('catalog_vocabulary'))
      ->set('catalog_menu', $form_state->getValue('catalog_menu'))
      ->save();
  }

}
