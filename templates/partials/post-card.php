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

use LiftedLogic\LLBag\PostType\MetaBoxes;

$beforeId  = (int) get_post_meta($post->ID, MetaBoxes::BEFORE_IMAGE_KEY, true);
$afterId   = (int) get_post_meta($post->ID, MetaBoxes::AFTER_IMAGE_KEY,  true);
$title     = get_field(MetaBoxes::TITLE_KEY, $post->ID) ?: get_the_title($post);
$permalink = get_permalink($post->ID);
?>

<a href="<?php echo esc_url($permalink); ?>" class="block overflow-hidden relative aspect-square group">

    <div class="flex absolute inset-0">
        <?php if ($beforeId) : ?>
        <div class="overflow-hidden w-1/2 h-full">
            <img
                src="<?php echo esc_url(wp_get_attachment_image_url($beforeId, 'medium_large')); ?>"
                alt="<?php esc_attr_e('Before', 'll-bag'); ?>"
                class="object-cover w-full h-full"
            >
        </div>
        <?php endif; ?>
        <?php if ($afterId) : ?>
        <div class="overflow-hidden w-1/2 h-full">
            <img
                src="<?php echo esc_url(wp_get_attachment_image_url($afterId, 'medium_large')); ?>"
                alt="<?php esc_attr_e('After', 'll-bag'); ?>"
                class="object-cover w-full h-full"
            >
        </div>
        <?php endif; ?>
    </div>

    <div class="absolute inset-y-0 left-1/2 w-px -translate-x-1/2 bg-white/60"></div>
    <div class="absolute inset-0 bg-black/20"></div>

    <span class="absolute bottom-3 left-3 text-sm font-medium leading-tight text-white">
        <?php echo esc_html($title); ?>
    </span>

</a>
