<?php
/**
 * Component: Related Before & Afters
 *
 * $component_data is provided by the theme's ll_format_component_data(),
 * which strips the layout name prefix from field names. Sub-fields must be
 * named '{layout_name}_{field_name}' so they arrive here as $component_data['{field_name}'].
 *
 * Override: place this file at {theme}/ll-before-after/components/RelatedBeforeAndAfters/related-before-and-afters.php
 */

defined('ABSPATH') || exit;

$content = $component_data['content'] ?? '';
$link    = $component_data['link']    ?? [];

?>

<div class="ll-ba-related-bna">
  <?php if ( $content ) : ?>
    <div class="wysiwyg">
      <?= $content ?>
    </div>
  <?php endif; ?>
  <?php if ( $link ) : ?>
    <a class="btn-primary" href="<?= $link['url']; ?>" <?= $link['target'] ? 'target="' . $link['target'] . '"' : '' ?>>
      <?= $link['title']; ?>
      <?php if($link['target'] === '_blank') : ?>
        <span class="sr-only"> (opens in new tab)</span>
      <?php endif; ?>
    </a>
  <?php endif; ?>
</div>
