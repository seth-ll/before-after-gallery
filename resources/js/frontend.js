import '../css/frontend.css';
import '../css/theme-before-after.css';

// Splide — import only if not already provided by the theme
import Splide from '@splidejs/splide';
import '@splidejs/splide/css';

import { initCardLinks }    from './card.js';
import { initRelatedSlider } from './related-posts.js';
import { initFilters }      from './filters.js';

// ── Splide: single post gallery + thumbnails ───────────────────────────────────

document.querySelectorAll( '.ba-single-page-slider' ).forEach( el => {
  const navEl = el.nextElementSibling?.classList.contains( 'ba-single-page-slider-nav' ) ? el.nextElementSibling : null;
  const primary = new Splide( el, {
    type: 'loop',
    perPage: 1,
    pagination: false,
    arrows: false,
    gap: '24px',
    focus: 'center',
    drag: false,
  } );

  if ( navEl ) {
    const nav = new Splide( navEl, {
      isNavigation: true,
      gap: '6px',
      pagination: false,
      arrows: false,
      fixedWidth: '80px',
      focus: 'center',
    } );
    primary.sync( nav );
    primary.mount();
    nav.mount();
  } else {
    primary.mount();
  }
} );

// ── Splide: static related sliders (dynamic ones are mounted in related-posts.js) ──

document.querySelectorAll( '.ba-related-slider' ).forEach( el => {
  if ( el.dataset.postId ) return;

  new Splide( el, {
    type: 'loop',
    perPage: 2,
    gap: '32px',
    pagination: false,
  } ).mount();
} );

// ── Splide: comparison slider ──────────────────────────────────────────────────

document.querySelectorAll( '.ba-comparison-slider' ).forEach( el => {
  const after   = el.querySelector( '.ba-comparison-slider__after' );
  const divider = el.querySelector( '.ba-comparison-slider__divider' );
  let dragging  = false;

  const setPosition = ( pct ) => {
    after.style.clipPath = `inset(0 ${ ( 1 - pct ) * 100 }% 0 0)`;
    divider.style.left   = `${ pct * 100 }%`;
  };

  setPosition( 0.5 );

  const getPct = ( x ) => {
    const rect = el.getBoundingClientRect();
    return Math.min( Math.max( ( x - rect.left ) / rect.width, 0 ), 1 );
  };

  el.addEventListener( 'pointerdown', ( e ) => {
    dragging = true;
    e.stopPropagation();
    el.setPointerCapture( e.pointerId );
    setPosition( getPct( e.clientX ) );
  } );
  el.addEventListener( 'pointermove', ( e ) => {
    if ( dragging ) setPosition( getPct( e.clientX ) );
  } );
  el.addEventListener( 'pointerup', () => { dragging = false; } );
} );

// ── Feature init ───────────────────────────────────────────────────────────────

initCardLinks();
initRelatedSlider();

document.addEventListener('DOMContentLoaded', () => {
  initFilters();
});
