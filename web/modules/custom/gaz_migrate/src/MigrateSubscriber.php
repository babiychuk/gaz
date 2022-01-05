<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 15.06.2018
 * Time: 11:27
 */

namespace Drupal\gaz_migrate;


use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\file\Entity\File;
use Drupal\file\FileUsage\FileUsageInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateRollbackEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MigrateSubscriber implements EventSubscriberInterface {

  /**
   * @var Connection
   */
  protected $connection;

  /**
   * @var EntityTypeManager;
   */
  protected $manager;

  /**
   * @var \Drupal\file\FileUsage\FileUsageInterface
   */
  // protected $file_usage;

  public function __construct(
    Connection $connection,
    EntityTypeManager $manager,
    FileUsageInterface $usage
  ) {
    $this->connection = $connection;
    $this->manager = $manager;
    $this->file_usage = $usage;
  }

  /**
   * Returns an array of event names this subscriber wants to listen to.
   *
   * The array keys are event names and the value can be:
   *
   *  * The method name to call (priority defaults to 0)
   *  * An array composed of the method name to call and the priority
   *  * An array of arrays composed of the method names to call and respective
   *    priorities, or 0 if unset
   *
   * For instance:
   *
   *  * array('eventName' => 'methodName')
   *  * array('eventName' => array('methodName', $priority))
   *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
   *
   * @return array The event names to listen to
   */
  public static function getSubscribedEvents() {
    // TODO: Implement getSubscribedEvents() method.
    $events = array();
    $events[MigrateEvents::POST_ROLLBACK][] = array('post_rollback');
    $events[MigrateEvents::POST_ROLLBACK][] = array('cleanup_files');
    return $events;
  }

  public function post_rollback(MigrateRollbackEvent $event) {
    $migration = $event->getMigration();
    $dest = $migration->getDestinationConfiguration();

    $dest_plugins = array(
      'entity:commerce_product',
      // 'entity:redirect',
      // 'entity:commerce_product_variation',
      'entity:node',
      // 'entity:user',
      'entity:taxonomy_term',
    );

    if (in_array($dest['plugin'], $dest_plugins)) {
      if ($dest['plugin'] == 'entity:node') {
        $bundle = $dest['default_bundle'];
      }

      $entity_type_name = explode(':', $dest['plugin'])[1];
      $entity_type = $this->manager->getDefinition($entity_type_name);
      $base_table = $entity_type->getBaseTable();
      $table_id = $entity_type->getKey('id');

      $query = $this->connection->select('file_usage', 'f');
      $query->leftJoin($base_table, 'e', "f.id = %alias.$table_id");
      $query->condition('f.type', $entity_type->id());
      $query->isNull("e.$table_id");
      $query->addField('f', 'fid');
      $fids = array_map(function ($row) {
        return $row->fid;
      }, $query->execute()->fetchAll());


      if (!empty($fids)) {
        $delete_query = $this->connection->delete('file_usage');
        $delete_query->condition('fid', $fids, 'IN');
        $delete_query->execute();
      }
    }
  }

  public function cleanup_files(MigrateRollbackEvent $event) {
    $fids = $this->manager->getStorage('file')->getQuery()->execute();
    $files = File::loadMultiple($fids);

    foreach ($files as $file) {
      /** @var File $file */
      $usage = $this->file_usage->listUsage($file);

      if (empty($usage)) {
        // $file->setTemporary();
        // $file->save();
        $file->delete();
      }
    }
  }
}
