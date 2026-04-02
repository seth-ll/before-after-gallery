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
use LiftedLogic\LLBag\Support\PostTerms;

$beforeId  = (int) get_post_meta($post->ID, MetaBoxes::BEFORE_IMAGE_KEY, true);
$afterId   = (int) get_post_meta($post->ID, MetaBoxes::AFTER_IMAGE_KEY,  true);
$permalink = get_permalink($post->ID);

$card = PostTerms::forCard($post->ID);
$visibleTerms = $card['visible'];
$overflow = $card['overflow'];
$cardLabel = $card['label'];
$termCount = count($card['terms']);

$pillClass = 'px-2 py-1 text-[11px] leading-tight text-black rounded-full bg-white max-w-[10rem] truncate';
?>

<a href="<?= esc_url($permalink); ?>" class="block overflow-hidden relative aspect-square outline group">
  <div class="flex absolute inset-0">
    <?php if ($beforeId) : ?>
      <div class="overflow-hidden w-1/2 h-full">
        <img
          src="<?= esc_url(wp_get_attachment_image_url($beforeId, 'medium_large')); ?>"
          alt="Before"
          class="object-cover size-full"
        >
      </div>
    <?php endif; ?>

    <?php if ($afterId) : ?>
      <div class="overflow-hidden w-1/2 h-full">
        <img
          src="<?= esc_url(wp_get_attachment_image_url($afterId, 'medium_large')); ?>"
          alt="After"
          class="object-cover w-full h-full"
        >
      </div>
    <?php endif; ?>
  </div>

  <div class="absolute inset-y-0 left-1/2 w-px -translate-x-1/2 bg-white/60"></div>
  <div class="absolute inset-0 bg-black/20"></div>

  <?php if ($termCount === 1) : ?>
    <div class="absolute right-0 bottom-0 left-0 p-3">
      <span class="<?= $pillClass; ?>"><?= esc_html($visibleTerms[0]); ?></span>
    </div>
  <?php elseif ($termCount > 1) : ?>

    <div class="absolute right-0 bottom-0 left-0 p-3 transition-opacity duration-200 pointer-events-none group-hover:opacity-0">
      <span class="<?= $pillClass; ?>">Multiple <?= esc_html($cardLabel); ?></span>
    </div>

    <div class="absolute right-0 bottom-0 left-0 p-3 opacity-0 transition-all duration-200 translate-y-2 pointer-events-none group-hover:x-[translate-y-0,opacity-100,pointer-events-auto]">
      <div class="flex flex-wrap gap-1">
        <?php foreach ($visibleTerms as $term) : ?>
          <span class="<?= $pillClass; ?>"><?= esc_html($term); ?></span>
        <?php endforeach; ?>
        <?php if ($overflow > 0) : ?>
          <span class="<?= $pillClass; ?>">+<?= $overflow; ?></span>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>
</a>
