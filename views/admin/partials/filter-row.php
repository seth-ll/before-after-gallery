<?php
/**
 * Admin partial: Single filter row in the filter settings table
 *
 * Available variables:
 *   $filter  array{id: string, label: string, meta_key: string, display: string, enabled: bool, searchable: bool, builtin: bool}
 */

defined('ABSPATH') || exit;

$id         = $filter['id'];
$isBuiltin  = !empty($filter['builtin']);
$isCheckbox = ($filter['display'] ?? 'checkbox') === 'checkbox';
?>

<tr class="ll-bag-filter-row" data-id="<?= esc_attr($id); ?>" draggable="true">
  <!-- Drag handle -->
  <td class="px-2 !text-2xl cursor-grab ll-bag-drag-handle" title="Drag to reorder">⠿</td>

  <td>
    <?php if ($isBuiltin) : ?>
      <!-- Builtins: label and slug are fixed, passed as hidden inputs -->
      <strong><?= esc_html($filter['label']); ?></strong>
      <input type="hidden" name="ll_bag_filters[<?= esc_attr($id); ?>][label]"    value="<?= esc_attr($filter['label']); ?>">
      <input type="hidden" name="ll_bag_filters[<?= esc_attr($id); ?>][meta_key]" value="<?= esc_attr($filter['meta_key']); ?>">
      <input type="hidden" name="ll_bag_filters[<?= esc_attr($id); ?>][builtin]"  value="1">
      <p class="mt-1 text-xs opacity-50"><?= esc_html($filter['meta_key']); ?></p>
    <?php else : ?>
      <input
        type="text"
        name="ll_bag_filters[<?= esc_attr($id); ?>][label]"
        value="<?= esc_attr($filter['label']); ?>"
        class="w-full ll-bag-label-input"
        required
      >
      <input
        type="hidden"
        name="ll_bag_filters[<?= esc_attr($id); ?>][meta_key]"
        value="<?= esc_attr($filter['meta_key']); ?>"
        class="ll-bag-meta-key-input"
      >
      <p class="mt-1 text-xs opacity-50 ll-bag-meta-key-hint">
        <?= esc_html($filter['meta_key']); ?>
      </p>
    <?php endif; ?>
  </td>

  <td>
    <select
      name="ll_bag_filters[<?= esc_attr($id); ?>][display]"
      class="ll-bag-display-select"
    >
      <option value="checkbox" <?php selected($filter['display'] ?? '', 'checkbox'); ?>>Checkbox</option>
      <option value="dropdown" <?php selected($filter['display'] ?? '', 'dropdown'); ?>>Dropdown</option>
    </select>

    <label class="ll-bag-searchable-wrap flex items-center gap-0.5 mt-2 text-xs <?= !$isCheckbox ? 'hidden' : ''; ?>">
      <input
        type="checkbox"
        name="ll_bag_filters[<?= esc_attr($id); ?>][searchable]"
        value="1"
        <?php checked(!empty($filter['searchable'])); ?>
        class="ll-bag-searchable-input"
      >
      Searchable
    </label>
  </td>

  <td class="">
    <input
      type="checkbox"
      name="ll_bag_filters[<?= esc_attr($id); ?>][enabled]"
      value="1"
      <?php checked(!empty($filter['enabled'])); ?>
    >
  </td>

  <td>
    <?php if (!$isBuiltin) : ?>
      <button type="button" class="button-link-delete ll-bag-remove-filter">Remove</button>
    <?php endif; ?>
  </td>
</tr>
