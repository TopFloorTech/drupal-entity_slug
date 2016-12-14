<?php

namespace Drupal\entity_slug\Plugin\Field\FieldType;

use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldType;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\entity_slug\Plugin\Slugifier\SlugifierInterface;
use Drupal\entity_slug\SlugifierManager;

/**
 * Provides a field type of slug.
 *
 * @FieldType(
 *   id = "slug",
 *   label = @Translation("Slug"),
 *   module = "slug_field",
 *   description = @Translation("Provides a Slug field type for generating URL-friendly identifiers."),
 *   default_widget = "slug_default",
 *   default_formatter = "slug_default",
 * )
 */
class SlugItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties = [];

    $properties['value'] = DataDefinition::create('string');
    $properties['input'] = DataDefinition::create('string');

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
        'input' => [
          'type' => 'varchar',
          'length' => 255,
          'not null' => FALSE,
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('input')->getValue();

    return $value === NULL || $value === '';
  }

  public static function defaultFieldSettings() {
    return [
      'slugifier_plugins' => ['token' => 'token', 'pathauto' => 'pathauto'],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $element = [];

    $settings = $this->getSettings();

    $element['slugifier_plugins'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Slugifier plugins'),
      '#description' => $this->t('Select the slugifier plugins to use for this field.'),
      '#options' => $this->getSlugifierOptions(),
      '#default_value' => $settings['slugifier_plugins'],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave() {
    $this->set('value', $this->slugify($this->get('input')->getValue()));

    parent::preSave();
  }

  /**
   * Handles calling slugification service.
   *
   * @param string $input
   *  The input to slugify.
   * @return string
   *  The slugified string.
   */
  protected function slugify($input) {
    /** @var SlugifierManager $manager */
    $manager = \Drupal::service('plugin.manager.slugifier');

    $settings = $this->getSettings();
    $slugifiers = $settings['slugifier_plugins'];

    $slug = $input;

    foreach ((array) $slugifiers as $pluginId => $name) {
      /** @var SlugifierInterface $slugifier */
      $slugifier = $manager->createInstance($pluginId);

      $slug = $slugifier->slugify($slug, $this->getEntity());
    }

    return $slug;
  }

  /**
   * Gets a list of options of Slugifier plugins
   */
  protected function getSlugifierOptions() {
    /** @var SlugifierManager $manager */
    $manager = \Drupal::service('plugin.manager.slugifier');

    $definitions = $manager->getDefinitions();

    uasort($definitions, function ($a, $b) {
      $aWeight = !empty($a['weight']) ? $a['weight'] : 0;
      $bWeight = !empty($b['weight']) ? $b['weight'] : 0;

      if ($aWeight == $bWeight) {
        return 0;
      }

      return ($aWeight < $bWeight) ? -1 : 1;
    });

    $options = [];

    foreach ($definitions as $pluginId => $definition) {
      $options[$pluginId] = $definition['name'];
    }

    return $options;
  }
}
