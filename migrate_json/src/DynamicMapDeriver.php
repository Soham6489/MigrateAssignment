<?php

namespace Drupal\migrate_json;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Deriver for the category translations.
 *
 * Used to remap the fields to property dynamically, fetched from
 * the admin user. Once the fields are updated in config form running
 * drush migrate update will re map the values in the fields.
 */
class DynamicMapDeriver extends DeriverBase implements ContainerDeriverInterface {
  /**
   * Config Factory .
   *
   * @var Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * Constructor function for the class.
   *
   * @param Drupal\Core\Config\ConfigFactory $config
   *   The Config Factory object to get configurations.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   * @param string $base_plugin_id
   *   The base plugin ID for the plugin ID.
   *
   * @return static
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Fetching all the mapped Values from the cofig.
    $mapValues = \Drupal::config('migrate_json.settings')->get('map_value');

    if (!empty($mapValues)) {
      foreach ($mapValues as $key => $value) {
        if (empty($value)) {
          continue;
        }
        $base_plugin_definition['process'][$key] = $value;
      }
    }

    $this->derivatives['tdrupal'] = $base_plugin_definition;

    return $this->derivatives;
  }

}
