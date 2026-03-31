<?php
/**
 * Template: Before & After posts archive (all posts)
 * URL:       /{archive-slug}
 *
 * Displays a grid of all published ll_before_after posts.
 * Override: place this file at {theme}/ll-before-after/archive-ll_before_after.php
 */

defined('ABSPATH') || exit;

use LiftedLogic\LLBag\Frontend\TemplateLoader;

get_header();
?>

<div class="ll-ba-archive">

    <?php if (have_posts()) : ?>
    <div class="grid grid-cols-2 md:grid-cols-4" id="ll-ba-grid">
        <?php while (have_posts()) : the_post();
            TemplateLoader::get('partials/post-card.php', ['post' => $GLOBALS['post']]);
        endwhile; ?>
    </div>
    <?php else : ?>
    <p class="py-12 text-center text-sm text-gray-500">No before &amp; after posts found.</p>
    <?php endif; ?>

</div>

<?php get_footer();
