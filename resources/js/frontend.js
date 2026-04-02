import '../css/frontend.css';

// Splide — import only if not already provided by the theme
import Splide from '@splidejs/splide';
import '@splidejs/splide/css';

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

document.querySelectorAll( '.ba-related-slider' ).forEach( el => {
  new Splide( el, {
    type: 'loop',
    perPage: 2,
    gap: '32px',
    pagination: false,
  } ).mount();
} );

document.querySelectorAll( '.ba-comparison-slider' ).forEach( el => {
  const after   = el.querySelector( '.ba-comparison-slider__after' );
  const divider = el.querySelector( '.ba-comparison-slider__divider' );
  let dragging  = false;

  const setPosition = ( pct ) => {
    after.style.clipPath = `inset(0 ${ ( 1 - pct ) * 100 }% 0 0)`;
    divider.style.left   = `${ pct * 100 }%`;
  };

  // Set initial 50/50 position
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

// Frontend entry point
// llBag global is set via wp_localize_script:
//   { ajaxUrl, nonce, action }

// TODO: implement filter UI interactions
//   - listen for checkbox/dropdown changes on #ll-ba-filters
//   - collect active filter values
//   - POST to llBag.ajaxUrl with nonce + filter params
//   - replace #ll-ba-grid innerHTML with response HTML
//   - update URL via history.pushState with active filter query params
//   - on page load, read query params and restore active filter state
