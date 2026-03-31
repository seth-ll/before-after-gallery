<?php

namespace LiftedLogic\LLBag\Filters;

use Illuminate\Support\Collection;

class FilterManager {
  public const OPTION_KEY = 'll_bag_filters';

  /**
   * Return all configured filters.
   *
   * Each filter is an array with keys: id, label, meta_key, display (checkbox|dropdown).
   *
   * @return Collection<int, array<string, string>>
   */
  public function all(): Collection {
    /** @var array<int, array<string, string>> $filters */
    $filters = get_option(self::OPTION_KEY, []);
    return collect($filters);
  }

  /**
   * Persist the full filter list.
   *
   * @param array<int, array<string, string>> $filters
   */
  public function save(array $filters): void {
    update_option(self::OPTION_KEY, $filters);
  }

  /**
   * Return all distinct meta values for a given key across published posts.
   *
   * @return Collection<int, string>
   */
  public static function getDistinctValues(string $metaKey): Collection {
    // TODO: implement
    // global $wpdb;
    // $results = $wpdb->get_col($wpdb->prepare(
    //     "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} pm
    //      INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
    //      WHERE pm.meta_key = %s AND p.post_type = %s AND p.post_status = 'publish'
    //      ORDER BY meta_value ASC",
    //     $metaKey,
    //     \LiftedLogic\LLBag\PostType\BeforeAfterPostType::SLUG
    // ));
    // return collect($results ?? []);
    return collect();
  }
}
