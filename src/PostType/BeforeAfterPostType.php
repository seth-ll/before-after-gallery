<?php

namespace LiftedLogic\LLBag\PostType;

use LiftedLogic\LLBag\Admin\SettingsPage;

class BeforeAfterPostType {
  public const SLUG = 'll_before_after';
  public const MENU_ICON = 'dashicons-camera';

  public function register(): void {
    add_action('init', [$this, 'registerPostType']);
    add_action('pre_get_posts', [$this, 'scopeCategoryArchive']);
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
    $pageId      = (int) get_field(SettingsPage::FIELD_POSTS_PAGE, 'option');
    $rewriteSlug = $pageId ? (get_page_uri($pageId) ?: 'll-before-after') : 'll-before-after';

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
