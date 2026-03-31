<?php
/**
 * Template: Single Before & After post
 *
 * Override: place this file at {theme}/ll-before-after/single-ll_before_after.php
 */

defined('ABSPATH') || exit;


$postId      = get_the_ID();
$beforeImage = get_post_meta($postId, \LiftedLogic\LLBag\PostType\MetaBoxes::BEFORE_IMAGE_KEY, true);
$afterImage  = get_post_meta($postId, \LiftedLogic\LLBag\PostType\MetaBoxes::AFTER_IMAGE_KEY,  true);
$gallery     = get_post_meta($postId, \LiftedLogic\LLBag\PostType\MetaBoxes::GALLERY_KEY,      true) ?: [];
$page_theme = '';
?>

<main class="ll-ba-single ba-grid ba-grid-cols-1 lg:ba-grid-cols-2 <?= $page_theme ?>">
    <div class="ba-p-16 ba-bg-background-fill ba-h-screen">
        <h4 class="hdg-5 text-text-heading">Treatments Used:</h4>
    </div>
    <div class="ba-h-full ba-relative ba-bg-cards-background">
        <div class="ba-sticky ba-top-[200px]">
            images
        </div>
    </div>
</main>

