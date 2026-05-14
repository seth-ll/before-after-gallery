<?php
/**
 * Template: Before & After category posts archive
 * URL:       /{archive-slug}/category/{slug}
 *
 * Displays a grid of ll_before_after posts within the current category.
 * Override: place this file at {theme}/ll-before-after/archive-ll_before_after_category.php
 */

defined('ABSPATH') || exit;

use LiftedLogic\LLBag\Frontend\TemplateLoader;
use LiftedLogic\LLBag\BeforeAfterPostType\BeforeAfterPostType;

$currentCategory = get_queried_object();

$ll_bag_header = apply_filters( 'll_bag/header_template', '' );
if ( $ll_bag_header !== false ) get_header( $ll_bag_header ?: null );
?>

<div class="ll-ba-archive-category">

  <div class="ll-ba-archive-category__header">
    <div class="ll-ba-archive-category__header-content">
      <?php if ( $currentCategory instanceof WP_Term ) : ?>
        <h1 class="ll-ba-archive-category__title"><?= esc_html( $currentCategory->name ) ?></h1>
        <?php if ( $currentCategory->description ) : ?>
          <p class="ll-ba-archive-category__description"><?= esc_html( $currentCategory->description ) ?></p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <a class="ll-ba-archive-category__back-link" href="<?= esc_url( BeforeAfterPostType::getCategoriesArchiveUrl() ) ?>">
      ← All Categories
    </a>
  </div>

  <?php if ( have_posts() ) : ?>
    <div class="ll-ba-archive-category__grid" id="ll-ba-grid">
      <?php while ( have_posts() ) : the_post();
        TemplateLoader::get( 'partials/post-card.php', ['post' => $GLOBALS['post']] );
      endwhile; ?>
    </div>
  <?php else : ?>
    <p class="ll-ba-archive-category__no-posts">No before &amp; after posts found.</p>
  <?php endif; ?>

</div>

<?php
$ll_bag_footer = apply_filters( 'll_bag/footer_template', '' );
if ( $ll_bag_footer !== false ) get_footer( $ll_bag_footer ?: null );
