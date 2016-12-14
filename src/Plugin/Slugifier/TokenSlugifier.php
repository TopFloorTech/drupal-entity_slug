<?php

namespace Drupal\entity_slug\Plugin\Slugifier;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\entity_slug\Annotation\Slugifier;

/**
 * @Slugifier(
 *   id = "token",
 *   name = @Translation("Token replacer"),
 *   weight = -50,
 * )
 */
class TokenSlugifier extends SlugifierBase {

  /**
   * {@inheritdoc}
   */
  public function slugify($input, FieldableEntityInterface $entity) {
    $slug = \Drupal::token()->replace($input, [
      $entity->getEntityTypeId() => $entity,
    ]);

    return $slug;
  }
}
