<?php

namespace LiftedLogic\LLBag\Integration;

class ThemeComponentInjector {

  // The LL theme's 'components' flexible content field key.
  // Identical across PHP-ComponentProvider sites and older JSON/DB sites.
  const COMPONENTS_FC_KEY = 'field_5d0d37adc1475';

  public function register(): void {
    add_filter( 'acf/load_field',                                              [$this, 'injectLayouts'] );
    add_filter( 'll-ba-related-bna_files',                                     [$this, 'injectRelatedBnaTemplate'] );
    add_filter( 'lifted_logic/component/format_data/ll_ba_related_bna',        [$this, 'formatRelatedBnaData'], 10, 3 );
  }

  public function formatRelatedBnaData( array $new_data, string $component_name, array $data ): array {
    // Map our content field. When ACF properly identifies it the key is
    // 'll_ba_related_bna_content'; when not (empty-string fallback), use $data[''].
    $new_data['content'] = $data['ll_ba_related_bna_content'] ?? $data[''] ?? '';
    return $new_data;
  }

  public function injectRelatedBnaTemplate( array $files ): array {
    $plugin_file = LL_BAG_PATH . 'components/RelatedBeforeAndAfters/related-before-and-afters.php';
    array_unshift( $files, $this->relativePathFromTheme( $plugin_file ) );
    return $files;
  }

  private function relativePathFromTheme( string $absolute_target ): string {
    $from_parts = explode( '/', trim( get_stylesheet_directory(), '/' ) );
    $to_parts   = explode( '/', trim( $absolute_target, '/' ) );

    while ( count( $from_parts ) && count( $to_parts ) && $from_parts[0] === $to_parts[0] ) {
      array_shift( $from_parts );
      array_shift( $to_parts );
    }

    return str_repeat( '../', count( $from_parts ) ) . implode( '/', $to_parts );
  }

  public function injectLayouts( array $field ): array {
    if ( $field['key'] !== self::COMPONENTS_FC_KEY ) {
      return $field;
    }

    $field['layouts']['layout_ll_ba_related_bna'] = $this->relatedBeforeAndAftersLayout();

    usort( $field['layouts'], fn( $a, $b ) => strcmp( $a['label'], $b['label'] ) );

    return $field;
  }

  private function relatedBeforeAndAftersLayout(): array {
    return [
      'key'     => 'layout_ll_ba_related_bna',
      'name'    => 'll_ba_related_bna',
      '_name'   => 'll_ba_related_bna',
      'label'   => 'Related Before & Afters',
      'display' => 'block',
      'layout'  => 'block',
      'min'     => '',
      'max'     => '',
      'sub_fields' => [
        [
          'key'   => 'field_ll_ba_rba_content',
          'label' => 'Content',
          'name'  => 'll_ba_related_bna_content',
          '_name' => 'll_ba_related_bna_content',
          'type'  => 'wysiwyg',
        ],
      ],
    ];
  }
}
