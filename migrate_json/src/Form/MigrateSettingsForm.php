<?php

namespace Drupal\migrate_json\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityFieldManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure Migrate Json settings for this site.
 *
 * This provides a file field to upload a json file to import from.
 * Also provides a list of valid fields in the custom entity to allow the
 * user to map the fields to the json file object properties during import.
 */
class MigrateSettingsForm extends ConfigFormBase {

  /**
   * Field Manager.
   *
   * @var Drupal\Core\Entity\EntityFieldManager
   */
  protected $entityFieldManager;

  /**
   * Options array for select.
   *
   * @var array
   */
  protected $list = [
    'city' => 'city',
    'loc' => 'loc',
    'pop' => 'pop',
    'state' => 'state',
  ];

  /**
   * Constructor function for the class.
   *
   * @param Drupal\Core\Entity\EntityFieldManager $entityFieldManager
   *   The Entity Type Manager object.
   */
  public function __construct(EntityFieldManager $entityFieldManager) {
    $this->entityFieldManager = $entityFieldManager;
  }

  /**
   * {@inheritdoc}
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The Drupal service container.
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'migrate_json_migrate_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['migrate_json.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('migrate_json.settings');

    // Using this Form element, the user can upload JSON file to import.
    $form['json_migrate'] = [
      '#type' => 'managed_file',
      '#title' => $this->t('Json File'),
      '#upload_validators' => [
        'file_validate_extensions' => ['json'],
      ],
      '#upload_location' => 'public://',
      '#default_value' => $config->get('migration_file_id'),
    ];

    // The container which holds all the fields to map.
    $form["map_fields"] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Choose the property to map the fields'),
    ];

    $entityFields = $this->getEntityFields();

    foreach ($entityFields as $field) {
      $form["map_fields"]["map_$field"] = [
        '#title' => "Map $field",
        '#type' => 'select',
        '#empty_option' => '-- Select --',
        '#required' => $field == 'label',
        '#description' => $this->t('Select property data to map'),
        '#options' => $this->list,
        '#default_value' => $config->get('map_value')[$field] ?? [],
      ];
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $mapValues = [];
    $entityFields = $this->getEntityFields();

    // Storing all the fields and property map in associative array.
    foreach ($entityFields as $field) {
      $mapValues[$field] = $form_state->getValue("map_$field");
    }

    // Storing the map values and the json file fid in config.
    $this->config('migrate_json.settings')
      ->set('map_value', $mapValues)
      ->set('migration_file_id', $form_state->getValue('json_migrate'))
      ->save();

    // Flushing the cache to update the migration mapping.
    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

  /**
   * Fetch all th current active fields.
   *
   * Assumed all the fields are of string type
   * and discarded the fields not required for mapping.
   *
   * @return array
   *   A array containing the field machine names.
   */
  protected function getEntityFields() {
    $fieldsArr = array_keys($this->entityFieldManager->getActiveFieldStorageDefinitions('mycustomentity'));
    return array_diff($fieldsArr, ['uuid', 'status', 'id', 'row_id']);
  }

}
