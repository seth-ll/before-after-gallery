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
use LiftedLogic\LLBag\Support\PostTerms;

$permalink = get_permalink($post->ID);

// Resolve card images from the first row of the ACF images repeater
$firstRow = (get_field('ll_ba_images', $post->ID) ?: [])[0] ?? [];
$option   = $firstRow['ll_ba_image_options'] ?? '';
$beforeId = null;
$afterId  = null;

if ($option === 'two-images') {
  $beforeId = $firstRow['ll_ba_before_image'] ?? null;
  $afterId  = $firstRow['ll_ba_after_image']  ?? null;
} else {
  // one-image and video both store the display image in ll_ba_single_image
  $beforeId = $firstRow['ll_ba_single_image'] ?? null;
}

$card         = PostTerms::forCard($post->ID);
$visibleTerms = $card['visible'];
$overflow     = $card['overflow'];
$cardLabel    = $card['label'];
$cardTaxonomy = $card['taxonomy'];
$termCount    = count($card['terms']);

$archiveUrl = get_post_type_archive_link(BeforeAfterPostType::SLUG);
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
