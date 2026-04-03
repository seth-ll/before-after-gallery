<?php

namespace LiftedLogic\LLBag\Admin;

use LiftedLogic\LLBag\PostType\BeforeAfterPostType;

class SettingsPage {
  public const FIELD_POSTS_PAGE = 'll_bag_posts_page';

  public function register(): void {
    add_action('acf/init',     [$this, 'registerOptionsPage']);
    add_action('acf/init',     [$this, 'registerFields']);
    add_action('acf/save_post', [$this, 'maybeFlushRewriteRules']);
  }

  public function maybeFlushRewriteRules(mixed $postId): void {
    if ($postId === 'options') {
      flush_rewrite_rules();
    }
  }

  public function registerOptionsPage(): void {
    if (!function_exists('acf_add_options_sub_page')) {
      return;
    }

    acf_add_options_sub_page([
      'page_title'  => __('Settings', 'll-bag'),
      'menu_title'  => __('Settings', 'll-bag'),
      'parent_slug' => 'edit.php?post_type=' . BeforeAfterPostType::SLUG,
      'capability'  => 'manage_options',
      'menu_slug'   => 'll-bag-settings',
    ]);
  }

  public function registerFields(): void {
    acf_add_local_field_group([
      'key'    => 'group_ll_ba_settings',
      'title'  => 'Before & After Settings',
      'fields' => [
        [
          'key' => 'field_ll_bag_archive_settings_tab',
          'label' => 'Archive Settings',
          'type' => 'tab',
          'placement' => 'left',
          'endpoint' => 0,
        ],
        [
          'key'           => 'field_ll_bag_posts_page',
          'label'         => 'All Posts Archive Page',
          'name'          => self::FIELD_POSTS_PAGE,
          'type'          => 'post_object',
          'post_type'     => ['page'],
          'return_format' => 'id',
          'allow_null'    => 1,
          'instructions'  => 'The page used for the "View All Before & Afters" link on the category archive.',
        ],
        [
          'key' => 'field_ll_bag_global_options_tab',
          'label' => 'Global Single Page Options',
          'type' => 'tab',
          'placement' => 'left',
          'endpoint' => 0,
        ],
        [
          'key' => 'field_ll_bag_cta_message',
          'type' => 'message',
          'message' => 'Leave CTA fields blank to omit CTA on single pages', 
        ],
        [
          'key' => 'field_ll_ba_global_cta_title',
          'label' => 'CTA Title',
          'name' => 'll_ba_global_cta_title',
          'type' => 'text',
          'wrapper' => [
            'width' => '50%',
          ],
        ],
        [
          'key' => 'field_ll_ba_global_cta_link',
          'label' => 'CTA Link',
          'name' => 'll_ba_global_cta_link',
          'type' => 'link',
          'return_format' => 'array',
          'wrapper' => [
            'width' => '50%',
          ],
        ],
        [
          'key' => 'field_ll_bag_related_treatments_slider_title',
          'label' => 'Related Treatments Slider Title',
          'name' => 'll_bag_related_treatments_slider_title',
          'type' => 'text',
        ],
        [
          'key' => 'field_ll_bag_archive_settings_tab_2',
          'label' => 'Archive Options',
          'type' => 'tab',
          'placement' => 'left',
          'endpoint' => 0,
        ],
        [
          'key'           => 'field_ll_ba_card_bg_color',
          'label'         => 'Card Background Color',
          'name'          => 'll_ba_card_bg_color',
          'type'          => 'color_picker',
          'default_value' => '#B8C2B0',
          'instructions'  => 'Background color shown behind the Before and After post cards.',
        ],
      ],
      'location' => [
        [
          [
              'param'    => 'options_page',
              'operator' => '==',
              'value'    => 'll-bag-settings',
          ],
        ],
      ],
    ]);
  }

  /**
   * Return the configured "all posts" page URL, or empty string if not set.
   */
  public static function getPostsPageUrl(): string {
    $pageId = (int) get_field(self::FIELD_POSTS_PAGE, 'option');
    return $pageId ? (string) get_permalink($pageId) : '';
  }
}
