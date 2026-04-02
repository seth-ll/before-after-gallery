<?php

namespace LiftedLogic\LLBag\Filters;

use Illuminate\Support\Collection;

class FilterManager {
  public const OPTION_KEY        = 'll_bag_filters';
  public const CARD_TAXONOMY_KEY = 'll_bag_card_taxonomy';

  private const BUILTINS = [
    ['id' => '__builtin_category', 'label' => 'Categories', 'meta_key' => 'category',  'builtin' => true, 'display' => 'checkbox', 'enabled' => false, 'searchable' => false],
    ['id' => '__builtin_post_tag', 'label' => 'Tags',       'meta_key' => 'post_tag',  'builtin' => true, 'display' => 'checkbox', 'enabled' => false, 'searchable' => false],
  ];

  /**
   * Return all configured filters with defaults
   *
   * Each filter: id, label, meta_key (= taxonomy slug), display (checkbox|dropdown), enabled (bool), searchable (bool).
   *
   * @return Collection<int, array<string, mixed>>
   */
  public function all(): Collection {
    /** @var array<int, array<string, mixed>> $filters */
    $filters = get_option(self::OPTION_KEY, []);

    $savedIds = array_column($filters, 'id');
    foreach (self::BUILTINS as $builtin) {
      if (!in_array($builtin['id'], $savedIds, true)) {
        $filters[] = $builtin;
      }
    }

    return collect($filters)->map(function (array $filter): array {
      return array_merge([
        'enabled'    => false,
        'searchable' => false,
        'builtin'    => false,
      ], $filter);
    });
  }

  /**
   * Return only filters that are enabled for the sidebar
   *
   * @return Collection<int, array<string, mixed>>
   */
  public function getEnabled(): Collection {
    return $this->all()->filter(fn(array $filter): bool => (bool) ($filter['enabled'] ?? false))->values();
  }

  public function getCardTaxonomy(): string {
    return (string) get_option(self::CARD_TAXONOMY_KEY, '');
  }

  public function saveCardTaxonomy(string $metaKey): void {
    update_option(self::CARD_TAXONOMY_KEY, $metaKey);
  }

  /**
   * @param array<int, array<string, mixed>> $filters
   */
  public function save(array $filters): void {
    update_option(self::OPTION_KEY, $filters);
  }
}
