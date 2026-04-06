<?php

namespace LiftedLogic\LLBag\Hooks;

class Hooks {

  public function register(): void {}

  /*
    SINGLE-LL_BEFORE_AFTER.PHP FILTERS
    Use add_filter( 'lifted_logic/bag/{hook}', ... ) to override output.
  */

  // Back button
  public static function bag_back_button_markup(): string {
    $classes = 'bag_back-text bag-inline-block';
    $text    = 'Back to Gallery';
    $refUrl  = isset($_GET['ba_ref']) ? wp_validate_redirect(wp_unslash($_GET['ba_ref']), '') : '';
    $href    = esc_url($refUrl ?: get_post_type_archive_link('ll_bag') ?: site_url('/'));
    $markup  = <<<HTML
      <a href="$href" class="$classes">$text</a>
    HTML;

    return apply_filters( 'lifted_logic/bag/bag_back_button_markup', $markup, $classes, $text, $href );
  }

  // CTA Link card
  public static function bag_link_card_markup( string $title, array $link ): string {
    if ( empty( $link ) ) return '';

    $href       = $link['url'] ?? '';
    $link_text  = $link['title'] ?? '';
    $target     = $link['target'] ? 'target="' . $link['target'] . '"' : '';
    $sr_text    = $link['target'] === '_blank' ? '<span class="sr-only"> (opens in new tab)</span>' : '';
    $markup     = <<<HTML
      <div class="border ba-p-5 ba-flex ba-gap-10 ba-justify-between ba-items-center theme-four bg-theme-background-fill">
        <p class="ba_hdg-6">$title</p>
        <a class="btn-primary" href="$href" $target>$link_text $sr_text</a>
      </div>
    HTML;

    return apply_filters( 'lifted_logic/bag/link_card_markup', $markup, $title, $link );
  }

}
