<?php

namespace Drupal\gaz_migrate\Plugin\migrate\source;

use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\file\Entity\File;
use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns bare-bones information about every available file entity.
 *
 * @MigrateSource(
 *   id = "gaz_media_entity_generator",
 * )
 */
class MediaEntityGenerator extends SourcePluginBase implements ContainerFactoryPluginInterface {

  /**
   * @var array*/
  protected $source_fields = [];

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  private $entity_field_manager;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entity_type_manager;

  /**
   * @var \Drupal\Core\Database\Connection
   */
  private $connection;

  /**
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  private $entity_query;

  /** @var Bool */
  private $only_default;

  /**
   * MediaEntityGenerator constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Core\Database\Connection $connection
   * @param \Drupal\Core\Entity\Query\QueryFactory $entity_query
   *
   * @throws \Drupal\migrate\MigrateException
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration,
    EntityFieldManagerInterface $entity_field_manager,
    EntityTypeManagerInterface $entity_type_manager,
    Connection $connection,
    QueryFactory $entity_query
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->entity_field_manager = $entity_field_manager;
    $this->entity_type_manager = $entity_type_manager;
    $this->connection = $connection;
    $this->entity_query = $entity_query;

    foreach ($this->configuration['field_names'] as $name) {
      $this->source_fields[$name] = $name;
    }

    if (isset($configuration['only_default'])) {
      $this->only_default = $configuration['only_default'];
    } else {
      $this->only_default = true;
    }
  }

  /**
   * {@inheritdoc}
   * @throws \Drupal\migrate\MigrateException
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
    MigrationInterface $migration = NULL
  ) {
    /** @var TYPE_NAME $plugin_id */
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('entity_field.manager'),
      $container->get('entity_type.manager'),
      $container->get('database'),
      $container->get('entity.query')
    );
  }

  /**
   * {@inheritdoc}
   * @throws \Exception
   */
  public function prepareRow(Row $row) {
    // Set source file.
    if (!empty($row->getSource()['target_id'])) {
      $file = File::load($row->getSource()['target_id']);
      $row->setSourceProperty('file_path', $file->getFileUri());
      $row->setSourceProperty('file_name', $file->getFilename());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'entity_id' => $this->t('Entity ID.'),
      'target_id' => $this->t('The file entity ID.'),
      'alt' => $this->t('The file arl.'),
      'title' => $this->t('The file title.'),
      'langcode' => $this->t('Lang code'),
      'langcode_original' => $this->t('Original language'),
      'width' => $this->t('Image width'),
      'height' => $this->t('Image height'),
      'file_path' => $this->t('File path'),
      'file_name' => $this->t('File name'),
      'uid' => $this->t('User ID'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return '';
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'target_id' => [
        'type' => 'integer',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  protected function initializeIterator() {
    $entityDefinition = $this->entity_type_manager->getDefinition($this->configuration['entity_type']);
    $bundleKey = $entityDefinition->getKey('bundle');

    $files_found = [];

    foreach ($this->source_fields as $name => $source_field) {

      $query = $this->entity_query->get($this->configuration['entity_type']);

      $query->condition($bundleKey, $this->configuration['bundle']);
      $query->condition("{$name}.target_id", 0, '>');
      $results = $query->execute();

      if ($results) {
        /** @var \Drupal\Core\Entity\ContentEntityInterface[] $entitites */
          $entitites = $this->entity_type_manager->getStorage($this->configuration['entity_type'])
            ->loadMultiple($results);

        foreach ($entitites as $id => $entity) {
          if ($this->only_default) {
            $languages = [$entity->language()];
          } else {
            $languages = $entity->getTranslationLanguages(FALSE);
          }
          foreach ($languages as $language) {
            $translation = $entity->getTranslation($language->getId());
            foreach ($translation->{$name}->getValue() as $reference) {
              if ($translation instanceof NodeInterface) {
                $uid = $translation->getOwnerId();
              } else {
                $uid = 1;
              }

              $files_found[] = [
                'entity_id' => $entity->id(),
                'target_id' => $reference['target_id'],
                'alt' => $reference['alt'],
                'title' => $reference['title'],
                'width' => $reference['width'],
                'height' => $reference['height'],
                'langcode' => $language->getId(),
                'langcode_oroginal' => $translation->getUntranslated()->language()->getId(),
                'is_default' => $translation->isDefaultTranslation() ? True : FALSE ,
                'uid' => $uid
              ];
            }
          }
        }
      }
    }
    return new \ArrayIterator($files_found);
  }

}
