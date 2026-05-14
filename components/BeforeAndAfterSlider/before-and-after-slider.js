// Component: Before & After Slider
import Splide from '@splidejs/splide';
import { getSensitiveMode, applySensitiveMode } from '../../resources/js/sensitive.js';

document.addEventListener( 'DOMContentLoaded', () => {
  const mode = getSensitiveMode();

  document.querySelectorAll( '.ll-ba-before-after-slider__splide' ).forEach( el => {
    const slider = new Splide( el, {
      type:       'loop',
      perPage:    1,
      gap:        '2rem',
      pagination: false,
    } ).mount();

    applySensitiveMode( el, mode );
  } );
} );
