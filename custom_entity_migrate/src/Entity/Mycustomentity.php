<?php

namespace Drupal\custom_entity_migrate\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\custom_entity_migrate\MycustomentityInterface;

/**
 * Defines the my custom entity entity class.
 *
 * @ContentEntityType(
 *   id = "mycustomentity",
 *   label = @Translation("My Custom Entity"),
 *   label_collection = @Translation("My Custom Entities"),
 *   label_singular = @Translation("my custom entity"),
 *   label_plural = @Translation("my custom entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count my custom entities",
 *     plural = "@count my custom entities",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\custom_entity_migrate\MycustomentityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "add" = "Drupal\custom_entity_migrate\Form\MycustomentityForm",
 *       "edit" = "Drupal\custom_entity_migrate\Form\MycustomentityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "mycustomentity",
 *   admin_permission = "administer mycustomentity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/mycustomentity",
 *     "add-form" = "/mycustomentity/add",
 *     "canonical" = "/mycustomentity/{mycustomentity}",
 *     "edit-form" = "/mycustomentity/{mycustomentity}/edit",
 *     "delete-form" = "/mycustomentity/{mycustomentity}/delete",
 *   },
 *   field_ui_base_route = "entity.mycustomentity.settings",
 * )
 */
class Mycustomentity extends ContentEntityBase implements MycustomentityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    // Adding the row_id field which will map the id property for migration.
    $fields['row_id'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Row Id'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -6,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -6,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Label'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE)
      ->setSetting('on_label', 'Enabled')
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => FALSE,
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'label' => 'above',
        'weight' => 0,
        'settings' => [
          'format' => 'enabled-disabled',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
