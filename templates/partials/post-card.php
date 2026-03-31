<?php
/**
 * Partial: Before & After post card
 *
 * Available variables:
 *   $post  WP_Post  The post object
 *
 * Override: place this file at {theme}/ll-before-after/partials/post-card.php
 */

defined('ABSPATH') || exit;

$beforeImage = get_post_meta($post->ID, \LiftedLogic\LLBag\PostType\MetaBoxes::BEFORE_IMAGE_KEY, true);
$afterImage  = get_post_meta($post->ID, \LiftedLogic\LLBag\PostType\MetaBoxes::AFTER_IMAGE_KEY,  true);
?>

<div class="ll-ba-card">
    <!-- TODO: implement post card layout (before/after images, title, link) -->
</div>
