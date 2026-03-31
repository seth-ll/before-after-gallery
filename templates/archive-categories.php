<?php
/**
 * Template: Category archive
 * Shortcode: [ba_categories]
 *
 * Available variables:
 *   $categories  WP_Term[]  All categories with ll_before_after posts
 *
 * Override: place this file at {theme}/ll-before-after/archive-categories.php
 */

defined('ABSPATH') || exit;
?>

<div class="ll-ba-categories">
    <?php foreach ($categories as $category) :
        \LiftedLogic\LLBag\Frontend\TemplateLoader::get('partials/category-card.php', ['category' => $category]);
    endforeach; ?>
</div>
