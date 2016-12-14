<?php

namespace Drupal\entity_slug\Plugin\Slugifier;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\entity_slug\Annotation\Slugifier;
use Drupal\pathauto\AliasCleaner;

/**
 * @Slugifier(
 *   id = "pathauto",
 *   name = @Translation("Pathauto cleaner"),
 *   weight = 50,
 * )
 */
class PathautoSlugifier extends SlugifierBase {

  /**
   * {@inheritdoc}
   */
  public function slugify($input, FieldableEntityInterface $entity) {
    /** @var AliasCleaner $aliasCleaner */
    $aliasCleaner = \Drupal::service('pathauto.alias_cleaner');

    $slug = $aliasCleaner->cleanString($input);

    return $slug;
  }
}
