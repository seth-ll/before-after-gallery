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

$images_field = get_field('field_ll_ba_images', $post->ID);
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
        ];
    }
}

$card_image = $ba_gallery_image[0] ?? null;

$is_stacked = $card_image
    && $card_image['option'] === 'two-images'
    && in_array( $card_image['ratio'], ['ba-single__ratio--wide', 'ba-single__ratio--panorama'] );
?>

<div class="ll-ba-card">

  <?php if ( $card_image ) : ?>

    <?php if ( $card_image['option'] === 'one-image' && $card_image['single_image_id'] ) : ?>
      <div class="ll-ba-card__image <?= $card_image['ratio'] ?>">
        <?php bag_include_partial( 'fit-image', [
          'image_id'       => $card_image['single_image_id'],
          'thumbnail_size' => 'large',
          'fit'            => 'object-cover',
          'position'       => 'object-center',
          'loading'        => true,
        ] ); ?>
      </div>

    <?php elseif ( $card_image['option'] === 'two-images' ) : ?>
      <div class="<?= $is_stacked ? 'll-ba-card__stacked' : 'll-ba-card__side-by-side' ?>">
        <?php if ( $card_image['before_image_id'] ) : ?>
          <div class="ll-ba-card__image <?= $card_image['ratio'] ?>">
            <?php bag_include_partial( 'fit-image', [
              'image_id'       => $card_image['before_image_id'],
              'thumbnail_size' => 'large',
              'fit'            => 'object-cover',
              'position'       => 'object-center',
              'loading'        => true,
            ] ); ?>
          </div>
        <?php endif; ?>
        <?php if ( $card_image['after_image_id'] ) : ?>
          <div class="ll-ba-card__image <?= $card_image['ratio'] ?>">
            <?php bag_include_partial( 'fit-image', [
              'image_id'       => $card_image['after_image_id'],
              'thumbnail_size' => 'large',
              'fit'            => 'object-cover',
              'position'       => 'object-center',
              'loading'        => true,
            ] ); ?>
          </div>
        <?php endif; ?>
      </div>

    <?php endif; ?>

  <?php endif; ?>

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

    <div class="ll-ba-card__hover-overlay"></div>

    <div class="ll-ba-card__pills ll-ba-card__pills--hover">
      
      <p class="ll-ba-card__hover-text">
        <?php 
          esc_html_e('View Details', 'll-bag')
        ?>
      </p>
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
