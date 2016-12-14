<?php

namespace Drupal\entity_slug\Plugin\Field\FieldWidget;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Field\Annotation\FieldWidget;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'slug' widget.
 *
 * @FieldWidget(
 *   id = "slug_default",
 *   module = "slug",
 *   label = @Translation("Slug field widget"),
 *   field_types = {
 *     "slug"
 *   }
 * )
 */
class SlugWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $value = isset($items[$delta]->input) ? $items[$delta]->input : '';

    $element += [
      '#type' => 'textfield',
      '#default_value' => $value,
      '#size' => '60',
      '#maxlength' => 255,
    ];

    return [
      'input' => $element,
      'token_help' => [
        '#theme' => 'token_tree_link',
        '#token_types' => 'all' // TODO: Limit to current entity type field is attached to
      ],
    ];
  }
}
