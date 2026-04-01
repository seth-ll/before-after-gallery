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
