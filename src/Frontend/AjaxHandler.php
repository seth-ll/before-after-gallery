<?php

namespace LiftedLogic\LLBag\Frontend;

use LiftedLogic\LLBag\Filters\FilterManager;
use LiftedLogic\LLBag\PostType\BeforeAfterPostType;

class AjaxHandler {
  public const ACTION = 'll_bag_filter';

  public function __construct(private readonly FilterManager $filterManager) {}

  public function register(): void {
    add_action('wp_ajax_nopriv_' . self::ACTION, [$this, 'handle']);
    add_action('wp_ajax_'        . self::ACTION, [$this, 'handle']);
  }

  public function handle(): void {
    check_ajax_referer(self::ACTION, 'nonce');

    $submitted = isset($_POST['filters']) && is_array($_POST['filters'])
                 ? $_POST['filters']
                 : [];

    // Build a set of valid taxonomy slugs from configured filters
    $validSlugs = $this->filterManager->all()->pluck('meta_key')->flip();

    $taxQuery = ['relation' => 'AND'];

    foreach ($submitted as $rawKey => $value) {
      $taxonomy = sanitize_key((string) $rawKey);

      // Only allow taxonomies that are configured as filters
      if (!$validSlugs->has($taxonomy)) {
        continue;
      }

      if (is_array($value) && !empty($value)) {
        $taxQuery[] = [
          'taxonomy' => $taxonomy,
          'field'    => 'slug',
          'terms'    => array_map('sanitize_key', $value),
          'operator' => 'IN',
        ];
      } elseif (is_string($value) && $value !== '') {
        $taxQuery[] = [
          'taxonomy' => $taxonomy,
          'field'    => 'slug',
          'terms'    => sanitize_key($value),
          'operator' => 'IN',
        ];
      }
    }

    $args = [
      'post_type'      => BeforeAfterPostType::SLUG,
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'orderby'        => 'date',
      'order'          => 'DESC',
    ];

    if (count($taxQuery) > 1) {
      $args['tax_query'] = $taxQuery;
    }

    $query = new \WP_Query($args);

    $html = '';
    foreach ($query->posts as $post) {
      $html .= TemplateLoader::render('partials/post-card.php', ['post' => $post]);
    }

    wp_send_json_success(['html' => $html, 'count' => $query->found_posts]);
  }
}
