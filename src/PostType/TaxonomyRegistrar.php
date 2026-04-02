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
    <div class="ll-ba-tax-tabs">
      <ul class="ll-ba-tax-tab-list">
        <?php foreach ($filters as $i => $filter) : ?>
          <li>
            <button
              type="button"
              class="ll-ba-tax-tab <?= $i === 0 ? 'is-active' : ''; ?>"
              data-target="ll-ba-tax-panel-<?= esc_attr($filter['meta_key']); ?>"
            ><?= esc_html($filter['label']); ?></button>
          </li>
        <?php endforeach; ?>
      </ul>

      <div class="ll-ba-tax-panels">
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
