<?php

namespace Drupal\entity_slug\Plugin\Slugifier;

use Drupal\search_api\Plugin\ConfigurablePluginBase;

/**
 * Abstract base class SlugifierBase
 *
 * @package Drupal\entity_slug\Plugin\Slugifier
 */
abstract class SlugifierBase extends ConfigurablePluginBase implements SlugifierInterface {

  /**
   * {@inheritdoc}
   */
  public function information() {
    return [];
  }
}
