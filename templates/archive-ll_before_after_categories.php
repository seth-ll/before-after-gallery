<?php
/**
 * Template: Before & After categories archive
 *
 * Displays a grid of all categories that have published ll_before_after posts.
 * Loaded when visiting the page set in B&A Settings → Categories Archive Page.
 * Override: place this file at {theme}/ll-before-after/archive-ll_before_after_categories.php
 */

defined('ABSPATH') || exit;

use LiftedLogic\LLBag\Admin\SettingsPage;
use LiftedLogic\LLBag\Frontend\TemplateLoader;

$categories = get_terms([
    'taxonomy'   => 'category',
    'hide_empty' => true,
]);

if (is_wp_error($categories)) {
    $categories = [];
}

get_header();
?>

<div class="ll-ba-archive-categories">

    <div class="flex justify-between items-center px-4 py-4 md:px-0">
        <?php $allPostsUrl = SettingsPage::getPostsPageUrl(); ?>
        <?php if ($allPostsUrl) : ?>
        <a href="<?php echo esc_url($allPostsUrl); ?>" class="flex gap-1 items-center text-sm">
            View All Before &amp; Afters <span aria-hidden="true">→</span>
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($categories)) : ?>
    <div class="grid grid-cols-2 md:grid-cols-4">
        <?php foreach ($categories as $category) :
            TemplateLoader::get('partials/category-card.php', ['category' => $category]);
        endforeach; ?>
    </div>
    <?php else : ?>
    <p class="py-12 text-sm text-center text-gray-500">No categories found.</p>
    <?php endif; ?>

</div>

<?php get_footer();
