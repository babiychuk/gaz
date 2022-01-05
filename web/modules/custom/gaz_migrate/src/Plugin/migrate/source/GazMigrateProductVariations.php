<?php

namespace Drupal\gaz_migrate\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Provides a 'GazMigrateUsers' migrate source.
 *
 * @MigrateSource(
 *  id = "gaz_migrate_product_variations"
 * )
 */
class GazMigrateProductVariations extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $join_vars = ['price', 'popular', 'image', 'action'];

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
    $query->condition('p.isfolder', 0);
    $query->condition('p.template', 5);
    $query->condition('uri', 'catalog/%', 'LIKE');
    $query->condition('p.published', 1);
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
      'var_price' => $this->t('Price'),
      'var_image' => $this->t('Image'),
      'var_action' => $this->t('Акционный товар'),
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
    if (!$ret) { return $ret; }

    $price = $row->getSourceProperty('var_price');
    $price = (float) preg_replace("/[^0-9,.]/", "", $price);
    $row->setSourceProperty('var_price', $price);

    $uri = $row->getSourceProperty('uri');
    $uri = rtrim($uri, '/');
    $row->setSourceProperty('uri', $uri);

    $action = $row->getSourceProperty('var_action');
    $action = (bool) $action;
    $row->setSourceProperty('var_action', (int) $action);

    $row->rehash();

    return true;
  }
}
