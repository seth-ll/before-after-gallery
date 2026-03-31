<?php
/**
 * Partial: Filter UI
 *
 * Available variables:
 *   $filters  Illuminate\Support\Collection  Filter config objects
 *             Each item: ['id', 'label', 'meta_key', 'display' => 'checkbox'|'dropdown']
 *
 * Override: place this file at {theme}/ll-before-after/partials/filters.php
 */

defined('ABSPATH') || exit;

if ($filters->isEmpty()) {
    return;
}
?>

<div class="ll-ba-filters" id="ll-ba-filters">
    <?php foreach ($filters as $filter) : ?>
        <div class="ll-ba-filters__group" data-filter-id="<?php echo esc_attr($filter['id']); ?>">
            <span class="ll-ba-filters__label"><?php echo esc_html($filter['label']); ?></span>

            <?php
            /** @var \Illuminate\Support\Collection $values */
            $values = \LiftedLogic\LLBag\Filters\FilterManager::getDistinctValues($filter['meta_key']);

            if ($filter['display'] === 'dropdown') : ?>
                <!-- TODO: dropdown markup -->
            <?php else : ?>
                <!-- TODO: checkbox markup -->
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
