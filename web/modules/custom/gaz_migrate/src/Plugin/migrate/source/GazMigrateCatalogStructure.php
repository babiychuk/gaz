<?php
/**
 * Created by PhpStorm.
 * User: snoopy
 * Date: 24.05.2018
 * Time: 11:07
 */

namespace Drupal\gaz_migrate\Plugin\migrate\source;


use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Provides a 'GazMigrateCatalogStructure' migrate source.
 *
 * @MigrateSource(
 *  id = "gaz_migrate_catalog_structure"
 * )
 */
class GazMigrateCatalogStructure extends SqlBase {
  /**
   * {@inheritdoc}
   */
  public function query() {
    $join_vars = ['seoDescription', 'seokeywords'];

    $query = $this->select('modx_site_content', 'p');

    $vars_query = $this->select('modx_site_tmplvars', 'vars');
    $vars_query->fields('vars', ['name', 'id']);
    $var_names = $vars_query->execute()->fetchAllKeyed();

    foreach ($join_vars as $join_var) {
      $name = 'var_' . $join_var;
      $query->leftJoin('modx_site_tmplvar_contentvalues', $name, '%alias.contentid = p.id AND %alias.tmplvarid = ' . $var_names[$join_var]);
      $query->addField($name, 'value', $name);
    }


    $query->condition('p.context_key', 'catalog');
    $query->condition('p.isfolder', 1);
    $query->condition('p.uri', 'catalog/%', 'LIKE');

    if ($this->configuration['skip_root']) {
      $query->condition('p.parent', 0, '!=');
      $query->leftJoin('modx_site_content', 'parent1', 'parent1.id = p.parent');
      $query->addField('parent1', 'parent', 'p1_parent');
    }

    $query->fields('p');
    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'id' => $this->t('ID'),
      'pagetitle' => $this->t('Page title'),
      'published' => $this->t('Is product is active'),
      'introtext' => $this->t('Content summary'),
      'content' => $this->t('HTML content'),
      'uri' => $this->t('URL path'),
      'parent' => $this->t('Parent ID'),
      'metatags' => $this->t('Meta tags'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return [
      'id' => [
        'type' => 'integer',
        'alias' => 'p'
      ],
    ];
  }

  function prepareRow(Row $row) {
    $ret = parent::prepareRow($row);
    if (!$ret) {
      return FALSE;
    }

    $metatags = array(
      'title' => $row->getSourceProperty('longtitle'),
      'description' => $row->getSourceProperty('seoDescription'),
      'keywords' => $row->getSourceProperty('seokeywords'),
    );
    $row->setSourceProperty('metatags', array_filter($metatags));

    $uri = $row->getSourceProperty('uri');
    $uri = rtrim($uri, '/');
    $row->setSourceProperty('uri', $uri);


    if ($this->configuration['skip_root']) {
      $parent1 = $row->getSourceProperty('p1_parent');

      if ($parent1 == 0) {
        $row->setSourceProperty('parent', 0);
      }
    }

    return TRUE;
  }
}
