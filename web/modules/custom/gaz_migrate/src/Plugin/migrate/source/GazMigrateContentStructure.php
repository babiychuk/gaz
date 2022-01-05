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
 *  id = "gaz_migrate_content_structure"
 * )
 */
class GazMigrateContentStructure extends SqlBase {
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

    $skip_aliases = [
      'korzina',
      'lichnyij-kabinet',
      'ajax',
      'sravnenie'
    ];


    $query->condition('context_key', 'web');
    $query->condition('isfolder', 1);
    $query->condition('published', 1);
    $query->condition('alias', $skip_aliases, 'NOT IN');
    // $query->condition('uri', 'catalog/%', 'LIKE');

    if (isset($this->configuration['skip_root']) && $this->configuration['skip_root']) {
      $query->condition('parent', 0, '!=');
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

    return TRUE;
  }
}
