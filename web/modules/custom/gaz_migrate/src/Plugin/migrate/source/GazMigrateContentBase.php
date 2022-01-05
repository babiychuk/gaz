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
 *
 */
class GazMigrateContentBase extends SqlBase {
  /**
   * {@inheritdoc}
   */
  public function query() {
    $join_vars = ['seoDescription', 'seokeywords', 'image'];

    $query = $this->select('modx_site_content', 'p');

    $vars_query = $this->select('modx_site_tmplvars', 'vars');
    $vars_query->fields('vars', ['name', 'id']);
    $var_names = $vars_query->execute()->fetchAllKeyed();

    foreach ($join_vars as $join_var) {
      $name = 'var_' . $join_var;
      $query->leftJoin('modx_site_tmplvar_contentvalues', $name, '%alias.contentid = p.id AND %alias.tmplvarid = ' . $var_names[$join_var]);
      $query->addField($name, 'value', $name);
    }

    $query->fields('p');

    if (isset($this->configuration['skip_ids']) && is_array($this->configuration['skip_ids'])) {
      $skip_ids =  $this->configuration['skip_ids'];
      $query->condition('p.id', $skip_ids, 'NOT IN');
    }

    $config_conditions = [
      'context_key',
      'isfolder',
      'published'
    ];

    foreach ($config_conditions as $condition) {
      if (isset($this->configuration[$condition])) {
        $query->condition($condition, $this->configuration[$condition]);
      }
    }

    if (isset($this->configuration['not_null'])  && is_array($this->configuration['not_null'])) {
      foreach ($this->configuration['not_null'] as $field) {
        $query->isNotNull($field);
      }
    }

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
      'createdon' => $this->t('Created date'),
      'editedon' => $this->t('The time that the node was last edited.'),
      'createdby' => $this->t('Author'),
      'var_image' => $this->t('Image')
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

  /**
   * @param \Drupal\migrate\Row $row
   *
   * @return bool
   * @throws \Exception
   */
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
