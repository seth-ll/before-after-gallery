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

$bgImageId   = get_field('ll_ba_category_bg_image', $category);
$bgImageUrl  = $bgImageId ? wp_get_attachment_image_url((int) $bgImageId, 'large') : '';
$archiveLink = get_post_type_archive_link(\LiftedLogic\LLBag\PostType\BeforeAfterPostType::SLUG);
$link        = $archiveLink
    ? trailingslashit($archiveLink) . 'category/' . $category->slug . '/'
    : get_category_link($category->term_id);
?>

<a href="<?php echo esc_url($link); ?>" class="block overflow-hidden relative aspect-video group">
    <?php if ($bgImageUrl) : ?>
    <img
      src="<?php echo esc_url($bgImageUrl); ?>"
      alt="<?php echo esc_attr($category->name); ?>"
      class="object-cover absolute inset-0 w-full h-full transition-transform duration-500 group-hover:scale-105"
    >
    <?php endif; ?>

    <div class="absolute inset-0 bg-black/40"></div>

    <span class="absolute bottom-3 left-3 text-sm font-medium leading-tight text-white">
      <?php echo esc_html($category->name); ?>
    </span>

</a>
