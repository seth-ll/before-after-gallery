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
  <div class="gap-8 ll-ba-archive__inner grid grid-cols-[275px,1fr]">
    <?php if ($filters->isNotEmpty()) : ?>
      <aside class="ll-ba-sidebar">
        <?php TemplateLoader::get('partials/filters.php', ['filters' => $filters]); ?>
      </aside>
    <?php endif; ?>

    <div class="min-w-0 ll-ba-content">
      <?php if (have_posts()) : ?>
        <div class="grid grid-cols-2 gap-8 md:grid-cols-4 [&.is-filtering]:opacity-50 duration-300" id="ll-ba-grid">
          <?php while (have_posts()) : the_post(); ?>
            <?php TemplateLoader::get('partials/post-card.php', ['post' => $GLOBALS['post']]); ?>
          <?php endwhile; ?>
        </div>
      <?php else : ?>

        <p class="py-12 text-sm text-center text-gray-500">No before &amp; after posts found.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php get_footer();
