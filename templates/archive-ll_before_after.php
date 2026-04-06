<?php
/**
 * Template: Before & After posts archive (all posts)
 * URL:       /{archive-slug}
 *
 * Displays a grid of all published ll_before_after posts with a configurable filter sidebar.
 * Override: place this file at {theme}/ll-before-after/archive-ll_before_after.php
 */

defined('ABSPATH') || exit;

use LiftedLogic\LLBag\Filters\FilterManager;
use LiftedLogic\LLBag\Frontend\TemplateLoader;

$filters = (new FilterManager())->getEnabled();

get_header();
?>

<div class="ll-ba-archive">
  <div class="ll-ba-archive__inner">
    <?php if ($filters->isNotEmpty()) : ?>
      <aside class="ll-ba-sidebar">
        <?php TemplateLoader::get('partials/filters.php', ['filters' => $filters]); ?>
      </aside>
    <?php endif; ?>

    <div class="ll-ba-content">
      <?php if (have_posts()) : ?>
        <div class="ll-ba-grid" id="ll-ba-grid">
          <?php while (have_posts()) : the_post(); ?>
            <?php TemplateLoader::get('partials/post-card.php', ['post' => $GLOBALS['post']]); ?>
          <?php endwhile; ?>
        </div>
      <?php else : ?>
        <p class="ll-ba-no-posts">No before &amp; after posts found.</p>
      <?php endif; ?>

      <div
        id="ll-ba-pagination"
        data-total-pages="<?= (int) $GLOBALS['wp_query']->max_num_pages; ?>"
        data-current-page="<?= max(1, (int) (get_query_var('paged') ?: ($_GET['paged'] ?? 1))); ?>"
      ></div>
    </div>
  </div>
</div>

<?php get_footer();
