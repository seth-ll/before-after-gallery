import '../css/frontend.css';
import '../css/theme-before-after.css';

// Splide — import only if not already provided by the theme
import Splide from '@splidejs/splide';
import '@splidejs/splide/css';

( function setHeaderHeight() {
  const header = document.querySelector( 'header' );
  if ( !header ) return;
  const adminBar = document.getElementById( 'wpadminbar' );
  const update = () => {
    const height = header.offsetHeight + ( adminBar ? adminBar.offsetHeight : 0 );
    document.documentElement.style.setProperty( '--ba-header-height', height + 'px' );
  };
  update();
  window.addEventListener( 'resize', update );
} )();

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
  const filtersEl = document.getElementById('ll-ba-filters');
  const filterGroupsEl = document.getElementById('ll-ba-filter-groups');
  const grid = document.getElementById('ll-ba-grid');

  if (!filtersEl || !grid) return;

  // ── Accordion toggles ──────────────────────────────────────────────────────

  filtersEl.addEventListener('click', (e) => {
    const toggle = e.target.closest('.ll-ba-filter-toggle');
    if (!toggle) return;

    const group   = toggle.closest('.ll-ba-filter-group');
    const content = group?.querySelector('.ll-ba-filter-content');
    const arrow   = toggle.querySelector('.ll-ba-filter-arrow');
    if (!content) return;

    const isOpen = !content.classList.contains('ll-ba-hidden');
    content.classList.toggle('ll-ba-hidden', isOpen);
    toggle.setAttribute('aria-expanded', String(!isOpen));
    arrow?.classList.toggle('rotate-180', !isOpen);
  });

  // ── Option search (client-side, filters visible checkboxes only) ───────────

  filtersEl.addEventListener('input', (e) => {
    const search = e.target.closest('.ll-ba-option-search');
    if (!search) return;

    const term = search.value.toLowerCase();
    const list = search.closest('.ll-ba-filter-content')?.querySelector('.ll-ba-checkbox-list');
    if (!list) return;

    list.querySelectorAll('.ll-ba-checkbox-option').forEach(opt => {
      const text = opt.textContent.toLowerCase();
      opt.style.display = text.includes(term) ? '' : 'none';
    });
  });

  // ── Filter change events (trigger AJAX) ───────────────────────────────────

  filtersEl.addEventListener('change', (e) => {
    if (
      e.target.matches('.ll-ba-checkbox-filter') ||
      e.target.matches('.ll-ba-dropdown-filter')
    ) {
      applyFilters();
    }
  });

  // Remove active tag
  document.getElementById('ll-ba-active-tags')?.addEventListener('click', (e) => {
    const btn = e.target.closest('.ll-ba-tag-remove');
    if (!btn) return;

    const { metaKey, value } = btn.dataset;
    const group = filterGroupsEl.querySelector(`[data-meta-key="${metaKey}"]`);
    if (!group) return;

    if (group.dataset.display === 'checkbox') {
      const checkbox = [...group.querySelectorAll('.ll-ba-checkbox-filter')]
        .find(el => el.value === value);
      if (checkbox) checkbox.checked = false;
    } else {
      const dropdown = group.querySelector('.ll-ba-dropdown-filter');
      if (dropdown) dropdown.value = '';
    }

    applyFilters();
  });

  // ── Clear all ──────────────────────────────────────────────────────────────

  document.getElementById('ll-ba-clear-all')?.addEventListener('click', () => {
    filtersEl.querySelectorAll('.ll-ba-checkbox-filter').forEach(el => { el.checked = false; });
    filtersEl.querySelectorAll('.ll-ba-dropdown-filter').forEach(el => { el.value = ''; });
    applyFilters();
  });

  // ── Core logic ─────────────────────────────────────────────────────────────

  function collectFilters() {
    const active = {};

    filtersEl.querySelectorAll('.ll-ba-filter-group').forEach(group => {
      const key     = group.dataset.metaKey;
      const display = group.dataset.display;

      if (display === 'checkbox') {
        const checkboxChecked = [...group.querySelectorAll('.ll-ba-checkbox-filter:checked')]
          .map(el => el.value);
        if (checkboxChecked.length) active[key] = checkboxChecked;
      } else {
        const dropdownValue = group.querySelector('.ll-ba-dropdown-filter')?.value ?? '';
        if (dropdownValue) active[key] = dropdownValue;
      }
    });

    return active;
  }

  async function applyFilters() {
    const active   = collectFilters();
    const formData = new FormData();

    formData.append('action', llBag.action);
    formData.append('nonce',  llBag.nonce);

    for (const [key, value] of Object.entries(active)) {
      if (Array.isArray(value)) {
        value.forEach(v => formData.append(`filters[${key}][]`, v));
      } else {
        formData.append(`filters[${key}]`, value);
      }
    }

    grid.classList.add('is-filtering');

    try {
      const res  = await fetch(llBag.ajaxUrl, { method: 'POST', body: formData });
      const data = await res.json();

      if (data.success) {
        grid.innerHTML = data.data.html || `<p class="col-span-full py-12 text-sm text-center text-gray-500">${llBag.noResults ?? 'No results found.'}</p>`;
      }
    } finally {
      grid.classList.remove('is-filtering');
    }

    updateActiveTags(active);
    updateUrl(active);
  }

  function updateActiveTags(active) {
    const bar  = document.getElementById('ll-ba-active-bar');
    const tags = document.getElementById('ll-ba-active-tags');
    if (!bar || !tags) return;

    tags.innerHTML = '';

    for (const [key, value] of Object.entries(active)) {
      const group  = filtersEl.querySelector(`[data-meta-key="${key}"]`);
      const label  = group?.dataset.label ?? key;
      const values = Array.isArray(value) ? value : [value];

      values.forEach(v => {
        // Use data-term-name for human-readable label (slug is the value)
        const checkbox = group?.querySelector(`.ll-ba-checkbox-filter[value="${v}"]`);
        const displayName = checkbox?.dataset.termName ?? v;

        const tag = document.createElement('li');
        tag.className = 'll-ba-tag';
        tag.innerHTML = `<button type="button" class="flex gap-1 items-center px-2 py-1 text-[11px] text-gray-700 bg-gray-100 rounded-full ll-ba-tag-remove hover:text-black" data-meta-key="${escAttr(key)}" data-value="${escAttr(v)}" aria-label="Remove filter">${escHtml(label)}: ${escHtml(displayName)} &times;</button>`;
        tags.appendChild(tag);
      });
    }

    bar.classList.toggle('ll-ba-hidden', tags.children.length === 0);
  }

  function updateUrl(active) {
    const params = new URLSearchParams();

    for (const [key, value] of Object.entries(active)) {
      if (Array.isArray(value)) {
        value.forEach(v => params.append(key, v));
      } else {
        params.set(key, value);
      }
    }

    const search = params.toString();
    history.pushState({}, '', search ? `?${search}` : location.pathname);
  }

  function restoreFromUrl() {
    const params = new URLSearchParams(location.search);
    if (!params.size) return;

    params.forEach((value, key) => {
      const group = filtersEl.querySelector(`[data-meta-key="${key}"]`);
      if (!group) return;

      if (group.dataset.display === 'checkbox') {
        const checkbox = [...group.querySelectorAll('.ll-ba-checkbox-filter')]
          .find(el => el.value === value);
        if (checkbox) checkbox.checked = true;
      } else {
        const dropdown = group.querySelector('.ll-ba-dropdown-filter');
        if (dropdown) dropdown.value = value;
      }
    });

    applyFilters();
  }

  // ── Helpers ────────────────────────────────────────────────────────────────

  function escHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function escAttr(str) {
    return str.replace(/"/g, '&quot;');
  }

  // ── Init ───────────────────────────────────────────────────────────────────

  restoreFromUrl();
  initFilters();
});
