// Component: Related Before & Afters
import { getSensitiveMode, applySensitiveMode } from '../../resources/js/sensitive.js';

document.addEventListener( 'DOMContentLoaded', () => {
  const mode = getSensitiveMode();

  document.querySelectorAll( '.ll-ba-related-bna__card-grid' ).forEach( grid => {
    applySensitiveMode( grid, mode );
  } );
} );
