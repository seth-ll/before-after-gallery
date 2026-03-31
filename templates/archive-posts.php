<?php
/**
 * Template: Filterable post archive
 * Shortcode: [ba_posts] or [ba_posts category="slug"]
 *
 * Available variables:
 *   $posts     WP_Post[]                    Initial post results
 *   $filters   Illuminate\Support\Collection Filter config objects
 *   $category  string                        Pre-filtered category slug (may be empty)
 *
 * Override: place this file at {theme}/ll-before-after/archive-posts.php
 */

defined('ABSPATH') || exit;
?>

<div class="ll-ba-posts" data-category="<?php echo esc_attr($category); ?>">

    <?php \LiftedLogic\LLBag\Frontend\TemplateLoader::get('partials/filters.php', ['filters' => $filters]); ?>

    <div class="ll-ba-posts__grid" id="ll-ba-grid">
        <?php foreach ($posts as $post) :
            \LiftedLogic\LLBag\Frontend\TemplateLoader::get('partials/post-card.php', ['post' => $post]);
        endforeach; ?>
    </div>

</div>
