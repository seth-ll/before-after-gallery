<?php

namespace LiftedLogic\LLBag\Support;

use LiftedLogic\LLBag\Filters\FilterManager;

class PostTerms {
  /**
   * Get term data for a post and taxonomy.
   *
   * Returns an array with:
   *   terms    - all matched term names
   *   visible  - term names up to $limit
   *   overflow - count of terms beyond $limit
   *   label    - taxonomy display name
   *
   * @return array{terms: string[], visible: string[], overflow: int, label: string}
   */
  public static function get(int $postId, string $taxonomy, int $limit = 5): array {
    $terms = wp_get_post_terms($postId, $taxonomy, ['fields' => 'names']);
    $terms = is_wp_error($terms) ? [] : $terms;

    $tax   = get_taxonomy($taxonomy);
    $label = $tax ? (string) $tax->labels->name : '';

    return [
      'terms'    => $terms,
      'visible'  => array_slice($terms, 0, $limit),
      'overflow' => max(0, count($terms) - $limit),
      'label'    => $label,
    ];
  }

  /**
   * Get term data for the taxonomy designated as the card display field.
   *
   * @return array{terms: string[], visible: string[], overflow: int, label: string}
   */
  public static function forCard(int $postId, int $limit = 5): array {
    $taxonomy = (new FilterManager())->getCardTaxonomy();

    if (!$taxonomy) {
      return ['terms' => [], 'visible' => [], 'overflow' => 0, 'label' => ''];
    }

    return self::get($postId, $taxonomy, $limit);
  }
}
