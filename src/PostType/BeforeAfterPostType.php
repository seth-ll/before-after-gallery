<?php

namespace LiftedLogic\LLBag\PostType;

use LiftedLogic\LLBag\Admin\SettingsPage;

class BeforeAfterPostType {
  public const SLUG = 'll_before_after';
  public const MENU_ICON = 'dashicons-camera';

  public function register(): void {
    add_action('init', [$this, 'registerPostType']);
    add_action('init', [$this, 'registerRewriteRules']);
    add_action('pre_get_posts', [$this, 'scopeCategoryArchive']);
    add_filter('query_vars', [$this, 'registerQueryVars']);
  }

  public function registerQueryVars(array $vars): array {
    $vars[] = 'll_ba_view';
    return $vars;
  }

  /**
   * Return the URL for the categories archive (/{archive-slug}/categories/).
   */
  public static function getCategoriesArchiveUrl(): string {
    $archiveLink = get_post_type_archive_link(self::SLUG);
    return $archiveLink ? trailingslashit($archiveLink) . 'categories/' : '';
  }

  private function getRewriteSlug(): string {
    $pageId = (int) get_field(SettingsPage::FIELD_POSTS_PAGE, 'option');
    return $pageId ? (get_page_uri($pageId) ?: 'll-before-after') : 'll-before-after';
  }

  public function registerRewriteRules(): void {
    $slug = $this->getRewriteSlug();
    add_rewrite_rule(
      '^' . preg_quote($slug, '/') . '/categories/?$',
      'index.php?ll_ba_view=categories',
      'top'
    );
    add_rewrite_rule(
      '^' . preg_quote($slug, '/') . '/category/([^/]+)/?$',
      'index.php?category_name=$matches[1]',
      'top'
    );
  }

  /**
   * Scope category archives so they only show ll_before_after posts.
   */
  public function scopeCategoryArchive(\WP_Query $query): void {
    if (!is_admin() && $query->is_main_query() && $query->is_category()) {
      $query->set('post_type', self::SLUG);
    }
  }

  public function registerPostType(): void {
    $rewriteSlug = $this->getRewriteSlug();

    register_post_type(self::SLUG, [
      'labels' => [
        'name'               => __('Before & After', 'll-bag'),
        'singular_name'      => __('Before & After', 'll-bag'),
        'add_new'            => __('Add New', 'll-bag'),
        'add_new_item'       => __('Add New Before & After', 'll-bag'),
        'edit_item'          => __('Edit Before & After', 'll-bag'),
        'new_item'           => __('New Before & After', 'll-bag'),
        'view_item'          => __('View Before & After', 'll-bag'),
        'search_items'       => __('Search Before & After', 'll-bag'),
        'not_found'          => __('No before & after posts found', 'll-bag'),
        'not_found_in_trash' => __('No before & after posts found in trash', 'll-bag'),
        'menu_name'          => __('B&A Posts', 'll-bag'),
      ],
      'public'          => true,
      'show_ui'         => true,
      'show_in_menu'    => true,
      'show_in_rest'    => true,
      'menu_icon'       => self::MENU_ICON,
      'menu_position'   => 25,
      'supports'        => ['title', 'thumbnail'],
      'taxonomies'      => ['category', 'post_tag'],
      'has_archive'     => $rewriteSlug,
      'rewrite'         => ['slug' => $rewriteSlug],
      'capability_type' => 'post',
    ]);
  }
}
