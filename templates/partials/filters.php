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
    <div class="flex flex-wrap gap-2 items-center py-3 border-b border-gray-200">
      <span class="text-sm text-gray-500 shrink-0">Filtered by:</span>
      <ul class="flex flex-wrap gap-2" id="ll-ba-active-tags">
        <!-- Tags are managed by updateActiveTags() in frontend.js -->
         <!-- we might want to add a way to expose that so we can style easier per site -->
      </ul>
      
      <button
        type="button"
        id="ll-ba-clear-all"
        class="ml-auto text-xs text-gray-500 underline hover:text-gray-900 shrink-0"
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
        class="border-b border-gray-200 ll-ba-filter-group"
        data-meta-key="<?= esc_attr($taxonomy); ?>"
        data-display="<?= esc_attr($display); ?>"
        data-label="<?= $label; ?>"
      >
        <button
          type="button"
          class="flex justify-between items-center py-4 w-full text-sm font-medium text-left text-gray-900 ll-ba-filter-toggle"
          aria-expanded="false"
        >
          <span><?= $label; ?></span>
  
          <svg class="w-4 h-4 transition-transform duration-200 ll-ba-filter-arrow shrink-0 [&.rotate-180]:rotate-180" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
          </svg>
        </button>
  
        <div class="hidden pb-4 ll-ba-filter-content">
  
          <?php if ($display === 'dropdown') : ?>
            <select class="px-3 py-2 w-full text-sm rounded border border-gray-300 ll-ba-dropdown-filter focus:outline-none focus:ring-1 focus:ring-current">
              <option value=""><?= 'All ' . $filter['label']; ?></option>
              <?php foreach ($terms as $term) : ?>
                <option value="<?= esc_attr($term->slug); ?>"><?= esc_html($term->name); ?></option>
              <?php endforeach; ?>
            </select>
  
          <?php else : // checkbox ?>
            <?php if (!empty($filter['searchable'])) : ?>
              <div class="relative mb-3">
                <input
                  type="search"
                  class="px-3 py-2 pr-9 w-full text-sm rounded border border-gray-300 ll-ba-option-search focus:outline-none focus:ring-1 focus:ring-current"
                  placeholder="<?= 'Search ' . $filter['label'];?>"
                >
                <svg class="absolute right-3 top-1/2 w-4 h-4 text-gray-400 -translate-y-1/2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd"/>
                </svg>
              </div>
            <?php endif; ?>
  
            <div class="flex flex-col gap-y-2 ll-ba-checkbox-list">
              <?php foreach ($terms as $term) : ?>
                <label class="flex gap-2 items-center text-sm cursor-pointer ll-ba-checkbox-option">
                  <input
                    type="checkbox"
                    class="rounded border-gray-300 ll-ba-checkbox-filter"
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
