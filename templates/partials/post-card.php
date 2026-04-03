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

use LiftedLogic\LLBag\PostType\BeforeAfterPostType;
use LiftedLogic\LLBag\PostType\MetaBoxes;
use LiftedLogic\LLBag\Support\PostTerms;

$beforeId  = (int) get_post_meta($post->ID, MetaBoxes::BEFORE_IMAGE_KEY, true);
$afterId   = (int) get_post_meta($post->ID, MetaBoxes::AFTER_IMAGE_KEY,  true);
$permalink = get_permalink($post->ID);

$card         = PostTerms::forCard($post->ID);
$visibleTerms = $card['visible'];
$overflow     = $card['overflow'];
$cardLabel    = $card['label'];
$cardTaxonomy = $card['taxonomy'];
$termCount    = count($card['terms']);

$archiveUrl = get_post_type_archive_link(BeforeAfterPostType::SLUG);

$images_field = get_field('field_ll_ba_images');
$ba_gallery_image = [];
if ( !empty($images_field) ) {
    foreach ( $images_field as $image ) {
        $ratio_class = 'ba-single__ratio--square';
        if ( $image['ll_ba_image_ratio'] === 'wide' ) {
            $ratio_class = 'ba-single__ratio--wide';
        } elseif ( $image['ll_ba_image_ratio'] === 'panorama' ) {
            $ratio_class = 'ba-single__ratio--panorama';
        } elseif ( $image['ll_ba_image_ratio'] === 'vertical' ) {
            $ratio_class = 'ba-single__ratio--vertical';
        }
        $ba_gallery_image[] = [
            'option'           => $image['ll_ba_image_options'],
            'ratio'            => $ratio_class,
            'single_image_id'  => $image['ll_ba_single_image'],
            'before_image_id'  => $image['ll_ba_before_image'],
            'after_image_id'   => $image['ll_ba_after_image'],
            'video_url'        => $image['ll_ba_video_url'],
            'video_title'      => $image['ll_ba_video_title'],
            'comparison_slider'=> $image['ll_ba_comparison_slider'],
        ];
    }
}
?>

<div class="ll-ba-card">
  <div class="ll-ba-card__images">
    <?php if ($beforeId) : ?>
      <div class="ll-ba-card__half">
        <img
          src="<?= esc_url(wp_get_attachment_image_url($beforeId, 'medium_large')); ?>"
          alt="Before"
          class="ll-ba-card__img"
        >
      </div>
    <?php endif; ?>

    <?php if ($afterId) : ?>
      <div class="ll-ba-card__half">
        <img
          src="<?= esc_url(wp_get_attachment_image_url($afterId, 'medium_large')); ?>"
          alt="After"
          class="ll-ba-card__img"
        >
      </div>
    <?php endif; ?>
  </div>

  <div class="ll-ba-card__divider"></div>
  <div class="ll-ba-card__overlay"></div>

  <!-- Card link overlay — sits beneath the pills -->
  <a href="<?= esc_url($permalink); ?>" class="ll-ba-card__link" aria-label="<?= esc_attr(get_the_title($post)); ?>"></a>

  <?php if ($termCount === 1) : ?>

    <div class="ll-ba-card__pills">
      <a
        href="<?= esc_url(add_query_arg($cardTaxonomy, $visibleTerms[0]->slug, $archiveUrl)); ?>"
        class="ll-ba-card__pill"
      >
        <?= $visibleTerms[0]->name; ?>
      </a>
    </div>

  <?php elseif ($termCount > 1) : ?>

    <div class="ll-ba-card__pills ll-ba-card__pills--default">
      <span class="ll-ba-card__pill">Multiple <?= esc_html($cardLabel); ?></span>
    </div>

    <div class="ll-ba-card__pills ll-ba-card__pills--hover">
      <div class="ll-ba-card__pill-group">
        <?php foreach ($visibleTerms as $term) : ?>
          <a
            href="<?= esc_url(add_query_arg($cardTaxonomy, $term->slug, $archiveUrl)); ?>"
            class="ll-ba-card__pill"
          >
            <?= esc_html($term->name); ?>
          </a>
        <?php endforeach; ?>
        <?php if ($overflow > 0) : ?>
          <span class="ll-ba-card__pill">+<?= $overflow; ?></span>
        <?php endif; ?>
      </div>
    </div>

  <?php endif; ?>
</div>
