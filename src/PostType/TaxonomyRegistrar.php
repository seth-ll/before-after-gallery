<?php

namespace LiftedLogic\LLBag\PostType;

use Illuminate\Support\Collection;
use LiftedLogic\LLBag\Filters\FilterManager;

class TaxonomyRegistrar {
  public function __construct(private readonly FilterManager $filterManager) {}

  public function register(): void {
    add_action('init', [$this, 'registerTaxonomies']);
    add_action('add_meta_boxes_' . BeforeAfterPostType::SLUG, [$this, 'registerMetaBoxes']);
  }

  public function registerMetaBoxes(): void {
    $filters = $this->filterManager->all()
      ->filter(fn(array $f) => !empty($f['meta_key']) && empty($f['builtin']) && taxonomy_exists($f['meta_key']))
      ->values();

    if ($filters->isEmpty()) return;

    foreach ($filters as $filter) {
      remove_meta_box($filter['meta_key'] . 'div', BeforeAfterPostType::SLUG, 'side');
    }

    add_meta_box(
      'll-ba-taxonomy-tabs',
      'Attributes',
      fn(\WP_Post $post) => $this->renderTaxonomyTabs($post, $filters),
      BeforeAfterPostType::SLUG,
      'normal',
    );
  }

  public function renderTaxonomyTabs(\WP_Post $post, Collection $filters): void {
    ?>
    <div class="grid grid-cols-[150px,1fr] grid-rows-1 min-h-[200px] ll-ba-tax-tabs">
      <ul class="-ml-3 list-none ll-ba-tax-tab-list">
        <?php foreach ($filters as $i => $filter) : ?>
          <li class="m-0 border-b border-gray-200">
            <button
              type="button"
              class="ll-ba-tax-tab <?= $i === 0 ? 'is-active' : ''; ?>
              w-full text-left py-2 bg-gray-100 pl-2 pr-4 border-r-2 border-transparent duration-300
              hover:x-[text-blue-500,bg-white]
              [&.is-active]:x-[bg-white,text-blue-500,border-blue-500]
              "
              data-target="ll-ba-tax-panel-<?= esc_attr($filter['meta_key']); ?>"
            >
              <?= $filter['label']; ?>
            </button>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="px-2 ll-ba-tax-panels">
        <?php foreach ($filters as $i => $filter) :
          $slug = $filter['meta_key'];
          if (!get_taxonomy($slug)) continue;
        ?>
          <div
            id="ll-ba-tax-panel-<?= esc_attr($slug); ?>"
            class="ll-ba-tax-panel"
            <?= $i !== 0 ? 'hidden' : ''; ?>
          >
            <?php post_categories_meta_box($post, ['args' => ['taxonomy' => $slug]]); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php
  }

  public function registerTaxonomies(): void {
    foreach ($this->filterManager->all() as $filter) {
      $slug = $filter['meta_key'] ?? '';

      if ($slug === '' || taxonomy_exists($slug)) {
        continue;
      }

      $label = $filter['label'];

      register_taxonomy($slug, BeforeAfterPostType::SLUG, [
        'labels' => [
          'name'          => $label,
          'singular_name' => $label,
          'add_new_item'  => "Add {$label}",
          'new_item_name' => "New {$label} Name",
        ],
        'hierarchical'      => true,
        'show_ui'           => true,
        'show_admin_column' => false,
        'show_in_rest'      => true,
        'show_in_nav_menus' => false,
        'rewrite'           => ['slug' => $slug],
      ]);
    }
  }
}
