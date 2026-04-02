<?php

namespace LiftedLogic\LLBag\PostType;

use LiftedLogic\LLBag\Filters\FilterManager;

class TaxonomyRegistrar {
  public function __construct(private readonly FilterManager $filterManager) {}

  public function register(): void {
    add_action('init', [$this, 'registerTaxonomies']);
  }

  public function registerTaxonomies(): void {
    foreach ($this->filterManager->all() as $filter) {
      $slug = $filter['meta_key'] ?? '';

      if ($slug === '' || taxonomy_exists($slug)) {
        continue;
      }

      register_taxonomy($slug, BeforeAfterPostType::SLUG, [
        'label'             => $filter['label'],
        'hierarchical'      => false,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_rest'      => true,
        'show_in_nav_menus' => false,
        'rewrite'           => ['slug' => $slug],
      ]);
    }
  }
}
