<?php
/**
 * Partial: Filter sidebar
 *
 * Available variables:
 *   $filters  Illuminate\Support\Collection  Enabled filter config objects
 *             Each item: ['id', 'label', 'meta_key' (= taxonomy slug), 'display' => 'checkbox'|'dropdown', 'searchable' => bool]
 *
 * Override: place this file at {theme}/ll-before-after/partials/filters.php
 */

defined('ABSPATH') || exit;

if ($filters->isEmpty()) {
  return;
}
?>

<div class="ll-ba-filters" id="ll-ba-filters">

  <!-- Filtered by tags -->
  <div class="hidden ll-ba-filters__active" id="ll-ba-active-bar">
    <div class="ll-ba-filters__active-inner">
      <span class="ll-ba-filters__active-label">Filtered by:</span>
      <ul class="ll-ba-filters__active-tags" id="ll-ba-active-tags">
        <!-- Tags are managed by updateActiveTags() in frontend.js -->
      </ul>
      <button
        type="button"
        id="ll-ba-clear-all"
        class="ll-ba-filters__clear-all"
      >
        Clear All
      </button>
    </div>
  </div>

  <!-- Filter groups -->
  <ul id="ll-ba-filter-groups">
    <?php foreach ($filters as $filter) :
      $taxonomy = $filter['meta_key'] ?? '';
      $display  = $filter['display'] ?? 'checkbox';
      $label    = esc_html($filter['label']);

      $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false]);
      if (is_wp_error($terms) || empty($terms)) continue;
    ?>
      <li
        class="ll-ba-filter-group"
        data-meta-key="<?= esc_attr($taxonomy); ?>"
        data-display="<?= esc_attr($display); ?>"
        data-label="<?= $label; ?>"
      >
        <button
          type="button"
          class="ll-ba-filter-toggle"
          aria-expanded="false"
        >
          <span><?= $label; ?></span>

          <svg class="ll-ba-filter-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
          </svg>
        </button>

        <div class="hidden ll-ba-filter-content">

          <?php if ($display === 'dropdown') : ?>
            <select class="ll-ba-dropdown-filter">
              <option value=""><?= 'All ' . $filter['label']; ?></option>
              <?php foreach ($terms as $term) : ?>
                <option value="<?= esc_attr($term->slug); ?>"><?= esc_html($term->name); ?></option>
              <?php endforeach; ?>
            </select>

          <?php else : // checkbox ?>
            <?php if (!empty($filter['searchable'])) : ?>
              <div class="ll-ba-option-search-wrap">
                <input
                  type="search"
                  class="ll-ba-option-search"
                  placeholder="<?= 'Search ' . $filter['label'];?>"
                >
                <svg class="ll-ba-option-search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
              </div>
            <?php endif; ?>

            <div class="ll-ba-checkbox-list">
              <?php foreach ($terms as $term) : ?>
                <label class="ll-ba-checkbox-option">
                  <input
                    type="checkbox"
                    class="ll-ba-checkbox-filter"
                    value="<?= esc_attr($term->slug); ?>"
                    data-term-name="<?= esc_attr($term->name); ?>"
                  >
                  <?= esc_html($term->name); ?>
                </label>
              <?php endforeach; ?>
            </div>

          <?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
  </ul>

</div>
