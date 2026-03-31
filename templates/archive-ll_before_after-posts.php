<?php
/**
 * Template: Before & After posts archive (by category)
 * URL:       /category/{slug} (scoped to ll_before_after posts via scopeCategoryArchive)
 *
 * Displays a grid of ll_before_after posts within the current category.
 * Override: place this file at {theme}/ll-before-after/archive-ll_before_after-posts.php
 */

defined('ABSPATH') || exit;

use LiftedLogic\LLBag\Frontend\TemplateLoader;
use LiftedLogic\LLBag\PostType\BeforeAfterPostType;

$currentCategory = get_queried_object();

get_header();
?>

<div class="ll-ba-archive-posts">

    <div class="flex items-center justify-between px-4 py-4 md:px-0">
        <div>
            <?php if ($currentCategory instanceof WP_Term) : ?>
            <h1 class="text-2xl font-semibold"><?php echo esc_html($currentCategory->name); ?></h1>
            <?php if ($currentCategory->description) : ?>
            <p class="mt-1 text-sm text-gray-600"><?php echo esc_html($currentCategory->description); ?></p>
            <?php endif; ?>
            <?php endif; ?>
        </div>
        <a href="<?php echo esc_url(get_post_type_archive_link(BeforeAfterPostType::SLUG)); ?>" class="text-sm flex items-center gap-1">
            <span aria-hidden="true">←</span> All Categories
        </a>
    </div>

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
