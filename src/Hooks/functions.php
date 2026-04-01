<?php

// No namespace — these must be available globally.

if ( ! function_exists( 'bag_include_partial' ) ) {
  function bag_include_partial( string $partial, array $component_data = [], array $component_args = [] ): void {
    include plugin_dir_path( LL_BAG_FILE ) . "templates/partials/{$partial}.php";
  }
}
