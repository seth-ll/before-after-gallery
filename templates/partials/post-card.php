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

<a href="<?php echo esc_url($permalink); ?>" class="relative block aspect-square overflow-hidden group">

    <div class="absolute inset-0 flex">
        <?php if ($beforeId) : ?>
        <div class="w-1/2 h-full overflow-hidden">
            <img
                src="<?php echo esc_url(wp_get_attachment_image_url($beforeId, 'medium_large')); ?>"
                alt="<?php esc_attr_e('Before', 'll-bag'); ?>"
                class="w-full h-full object-cover"
            >
        </div>
        <?php endif; ?>
        <?php if ($afterId) : ?>
        <div class="w-1/2 h-full overflow-hidden">
            <img
                src="<?php echo esc_url(wp_get_attachment_image_url($afterId, 'medium_large')); ?>"
                alt="<?php esc_attr_e('After', 'll-bag'); ?>"
                class="w-full h-full object-cover"
            >
        </div>
        <?php endif; ?>
    </div>

    <div class="absolute inset-y-0 left-1/2 w-px bg-white/60 -translate-x-1/2"></div>
    <div class="absolute inset-0 bg-black/20"></div>

    <span class="absolute bottom-3 left-3 text-white text-sm font-medium leading-tight">
        <?php echo esc_html($title); ?>
    </span>

</a>
