<?php
/**
 * Template: Single Before & After post
 *
 * Override: place this file at {theme}/ll-before-after/single-ll_before_after.php
 */

defined('ABSPATH') || exit;

get_header();

$postId      = get_the_ID();
$beforeImage = get_post_meta($postId, \LiftedLogic\LLBag\PostType\MetaBoxes::BEFORE_IMAGE_KEY, true);
$afterImage  = get_post_meta($postId, \LiftedLogic\LLBag\PostType\MetaBoxes::AFTER_IMAGE_KEY,  true);
$gallery     = get_post_meta($postId, \LiftedLogic\LLBag\PostType\MetaBoxes::GALLERY_KEY,      true) ?: [];
?>

<main class="ll-ba-single">
    <!-- TODO: implement single post layout -->
</main>

<?php get_footer();
