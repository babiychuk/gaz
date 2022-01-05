<?php

namespace Drupal\uag_blocks\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Provides a 'PostMetaInfo' block.
 *
 * @Block(
 *  id = "uag_post_meta",
 *  admin_label = @Translation("Post meta info"),
 *  context = {
 *    "node" = @ContextDefinition(
 *      "entity:node",
 *      label = @Translation("Current Node")
 *    )
 *  }
 * )
 */
class PostMetaInfo extends BlockBase implements ContainerFactoryPluginInterface {

  /** @var EntityTypeManagerInterface */
  protected $entityTypeManager;

  /** @var DateFormatterInterface */
  protected $dateFormatter;

  /**
   * Constructs a new PostMetaInfo object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param string $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   * @param \Drupal\Core\Datetime\DateFormatterInterface $dateFormatter
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    EntityTypeManagerInterface $entityTypeManager,
    DateFormatterInterface $dateFormatter
  ) {
    $this->entityTypeManager = $entityTypeManager;
    $this->dateFormatter = $dateFormatter;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('date.formatter')
    );
  }


  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function build() {
    /** @var \Drupal\node\NodeInterface $node */
    $node = $this->getContextValue('node');
    $build = [];

    if (!$node) {
      return $build;
    }

    /** @var \Drupal\node\NodeTypeInterface $bundle */
    $bundle = $this->entityTypeManager->getStorage('node_type')->load($node->bundle());
    if (!$bundle->displaySubmitted()) {
      return $build;
    }

    $date_formatted = $this->dateFormatter->format($node->getCreatedTime(), 'medium_no_time');
    //TODO: Create twig
    $build['uag_post_meta']['#markup'] = $this->t('Published on @date', [
      '@date' => $date_formatted
    ]);
    $build['#attributes']['class'] = [
      'node-type-' . $node->bundle(),
    ];
    // $build['#cache']

    return $build;
  }
}
