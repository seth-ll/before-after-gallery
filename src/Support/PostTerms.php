<?php

namespace LiftedLogic\LLBag\Support;

use LiftedLogic\LLBag\Filters\FilterManager;

class PostTerms {
  /**
   * Get term data for a post and taxonomy.
   *
   * Returns an array with:
   *   terms    - all matched WP_Term objects
   *   visible  - WP_Term objects up to $limit
   *   overflow - count of terms beyond $limit
   *   label    - taxonomy display name
   *   taxonomy - taxonomy slug
   *
   * @return array{terms: \WP_Term[], visible: \WP_Term[], overflow: int, label: string, taxonomy: string}
   */
  public static function get(int $postId, string $taxonomy, int $limit = 5): array {
    $terms = wp_get_post_terms($postId, $taxonomy);
    $terms = is_wp_error($terms) ? [] : $terms;

    $tax   = get_taxonomy($taxonomy);
    $label = $tax ? (string) $tax->labels->name : '';

    return [
      'terms'    => $terms,
      'visible'  => array_slice($terms, 0, $limit),
      'overflow' => max(0, count($terms) - $limit),
      'label'    => $label,
      'taxonomy' => $taxonomy,
    ];
  }

  /**
   * Get term data for the taxonomy designated as the card display field.
   *
   * @return array{terms: \WP_Term[], visible: \WP_Term[], overflow: int, label: string, taxonomy: string}
   */
  public static function forCard(int $postId, int $limit = 5): array {
    $taxonomy = (new FilterManager())->getCardTaxonomy();

    if (!$taxonomy) {
      return ['terms' => [], 'visible' => [], 'overflow' => 0, 'label' => '', 'taxonomy' => ''];
    }

    return self::get($postId, $taxonomy, $limit);
  }
}
