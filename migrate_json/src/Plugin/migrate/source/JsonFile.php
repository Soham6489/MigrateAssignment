<?php

namespace Drupal\migrate_json\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

/**
 * Source plugin to import data from JSON file.
 *
 * Used to fetch all the objects in the uploaded json file and
 * filter out the objects which does not contain the _id, considered
 * as unique identifier.
 *
 * @MigrateSource(
 *   id = "json_file"
 * )
 */
class JsonFile extends SourcePluginBase {
  /**
   * File Storage.
   *
   * @var Drupal\file\FileStorage
   */
  protected $fileStorage;

  /**
   * Config Factory .
   *
   * @var Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids = [
      'id' => [
        'type' => 'integer',
      ],
    ];
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return [
      'id' => $this->t('id'),
      'city' => $this->t('city'),
      'loc' => $this->t('loc'),
      'pop' => $this->t("pop"),
      'state' => $this->t("State"),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return "json data";
  }

  /**
   * Initializes the iterator with the source data.
   *
   * @return \Iterator
   *   An iterator containing the data for this source.
   */
  protected function initializeIterator() {
    $rows = [];
    // Fetching the fid from the configuration stored and
    // extracting the json data into an associative array.
    $fileId = $this->getConfig()->get('migrate_json.settings')->get('migration_file_id');
    if (isset($fileId[0])) {
      $file = $this->getFileStorage()->load($fileId[0]);
      $fileContent = json_decode(file_get_contents($file->getFileUri()), TRUE);
      foreach ($fileContent as $value) {
        // If Id property is not present skipping the object.
        if (!isset($value['_id'])) {
          continue;
        }
        $value['id'] = $value['_id'];
        $value['loc'] = implode(',', $value['loc']);
        unset($value['_id']);
        array_push($rows, $value);
      }
    }
    return new \ArrayIterator($rows);
  }

  /**
   * Gets the Config Factory object.
   *
   * @return \Drupal\Core\Config\ConfigFactory
   *   The Config Factorty object.
   */
  protected function getConfig() {
    if (!isset($this->config)) {
      $this->config = \Drupal::service('config.factory');
    }
    return $this->config;
  }

  /**
   * Gets the File Storage object.
   *
   * @return \Drupal\file\FileStorage
   *   The File Storage object.
   */
  protected function getFileStorage() {
    if (!isset($this->fileStorage)) {
      $this->fileStorage = \Drupal::service('entity_type.manager')->getStorage('file');
    }
    return $this->fileStorage;
  }

}
