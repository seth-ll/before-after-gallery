<?php
/**
 * Partial: Category card
 *
 * Available variables:
 *   $category  WP_Term  The category term object
 *
 * Override: place this file at {theme}/ll-before-after/partials/category-card.php
 */

defined('ABSPATH') || exit;

$bgImageId  = get_term_meta($category->term_id, 'll_ba_category_bg_image', true);
$bgImageUrl = $bgImageId ? wp_get_attachment_image_url((int) $bgImageId, 'large') : '';
$link       = get_category_link($category->term_id);
?>

<div class="ll-ba-category-card">
    <!-- TODO: implement category card layout (bg image, name, link) -->
</div>
