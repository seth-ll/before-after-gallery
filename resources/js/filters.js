import { renderPagination, initPagination } from './pagination.js';

export function initFilters() {
  const filtersEl      = document.getElementById('ll-ba-filters');
  const filterGroupsEl = document.getElementById('ll-ba-filter-groups');
  const grid           = document.getElementById('ll-ba-grid');
  const paginationEl   = document.getElementById('ll-ba-pagination');

  if (!filtersEl || !grid) return;

  let currentPage          = 1;
  let currentSensitiveMode = localStorage.getItem('ll-ba-sensitive-mode') || 'blur';

  // ── Accordion toggles ────────────────────────────────────────────────────────

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

  // ── Option search (client-side, filters visible checkboxes only) ─────────────

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

  // ── Filter change events ─────────────────────────────────────────────────────

  filtersEl.addEventListener('change', (e) => {
    if (
      e.target.matches('.ll-ba-checkbox-filter') ||
      e.target.matches('.ll-ba-dropdown-filter')
    ) {
      currentPage = 1;
      applyFilters();
    }
  });

  // ── Active tag removal ───────────────────────────────────────────────────────

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

    currentPage = 1;
    applyFilters();
  });

  // ── Sensitive images mode ────────────────────────────────────────────────────

  document.getElementById('ll-ba-sensitive-bar')?.addEventListener('click', (e) => {
    const btn = e.target.closest('.ll-ba-sensitive-btn');
    if (!btn) return;

    currentSensitiveMode = btn.dataset.mode;
    localStorage.setItem('ll-ba-sensitive-mode', currentSensitiveMode);
    applySensitiveMode(currentSensitiveMode);

    document.querySelectorAll('.ll-ba-sensitive-btn').forEach(b =>
      b.classList.toggle('is-active', b === btn)
    );
  });

  // ── Clear all ────────────────────────────────────────────────────────────────

  document.getElementById('ll-ba-clear-all')?.addEventListener('click', () => {
    filtersEl.querySelectorAll('.ll-ba-checkbox-filter').forEach(el => { el.checked = false; });
    filtersEl.querySelectorAll('.ll-ba-dropdown-filter').forEach(el => { el.value = ''; });
    currentPage = 1;
    applyFilters();
  });

  // ── Core ─────────────────────────────────────────────────────────────────────

  function collectFilters() {
    const active = {};

    filtersEl.querySelectorAll('.ll-ba-filter-group').forEach(group => {
      const key     = group.dataset.metaKey;
      const display = group.dataset.display;

      if (display === 'checkbox') {
        const checked = [...group.querySelectorAll('.ll-ba-checkbox-filter:checked')]
          .map(el => el.value);
        if (checked.length) active[key] = checked;
      } else {
        const dropdownValue = group.querySelector('.ll-ba-dropdown-filter')?.value ?? '';
        if (dropdownValue) active[key] = dropdownValue;
      }
    });

    return active;
  }

  async function applyFilters(page = currentPage) {
    currentPage = page;

    const active   = collectFilters();
    const formData = new FormData();

    formData.append('action', llBag.action);
    formData.append('nonce',  llBag.nonce);
    formData.append('paged',  page);

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
        renderPagination(paginationEl, data.data.total_pages, data.data.current_page, applyFilters);
        updateSensitiveBar();
        applySensitiveMode(currentSensitiveMode);
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
        const checkbox    = group?.querySelector(`.ll-ba-checkbox-filter[value="${v}"]`);
        const displayName = checkbox?.dataset.termName ?? v;

        const tag = document.createElement('li');
        tag.className = 'll-ba-tag';
        tag.innerHTML = `<button type="button" class="ll-ba-tag-remove" data-meta-key="${escAttr(key)}" data-value="${escAttr(v)}" aria-label="Remove filter">${escHtml(label)}: ${escHtml(displayName)} &times;</button>`;
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

    if (currentPage > 1) {
      params.set('paged', currentPage);
    }

    // Strip WP permalink pagination (/page/N/) so it doesn't conflict with JS state
    const basePath = location.pathname.replace(/\/page\/\d+\/?$/, '/');
    const search   = params.toString();
    history.pushState({}, '', search ? `${basePath}?${search}` : basePath);
  }

  function restoreFromUrl() {
    const params = new URLSearchParams(location.search);

    const pagedParam = params.get('paged');
    if (pagedParam) {
      currentPage = Math.max(1, parseInt(pagedParam, 10));
    }

    params.forEach((value, key) => {
      if (key === 'paged' || key === 'ba_ref') return;

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

    applyFilters(currentPage);
  }

  // ── Sensitive images helpers ─────────────────────────────────────────────────

  function updateSensitiveBar() {
    const bar = document.getElementById('ll-ba-sensitive-bar');
    if (!bar) return;
    const hasSensitive = grid.querySelectorAll('.ll-ba-card--sensitive').length > 0;
    bar.classList.toggle('ll-ba-hidden', !hasSensitive);
  }

  function applySensitiveMode(mode) {
    grid.querySelectorAll('.ll-ba-card').forEach(card => {
      card.classList.remove('is-blurred', 'is-hidden');
    });
    if (mode === 'blur') {
      grid.querySelectorAll('.ll-ba-card--sensitive').forEach(c => c.classList.add('is-blurred'));
    } else if (mode === 'hide') {
      grid.querySelectorAll('.ll-ba-card--sensitive').forEach(c => c.classList.add('is-hidden'));
    }
  }

  // ── Helpers ──────────────────────────────────────────────────────────────────

  function escHtml(str) {
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  }

  function escAttr(str) {
    return str.replace(/"/g, '&quot;');
  }

  // ── Init ─────────────────────────────────────────────────────────────────────

  const initParams = new URLSearchParams(location.search);

  let hasFilterParams = false;
  initParams.forEach((_, key) => {
    if (key !== 'paged' && key !== 'ba_ref') hasFilterParams = true;
  });

  const initPaged = initParams.get('paged');
  if (initPaged) {
    currentPage = Math.max(1, parseInt(initPaged, 10));
  }

  if (hasFilterParams) {
    restoreFromUrl();
  } else {
    if (!initPaged) {
      currentPage = initPagination(paginationEl, applyFilters);
    } else {
      const totalPages = parseInt(paginationEl?.dataset.totalPages ?? '1', 10);
      renderPagination(paginationEl, totalPages, currentPage, applyFilters);
    }
  }

  // ── Sensitive images init ─────────────────────────────────────────────────────

  document.querySelectorAll('.ll-ba-sensitive-btn').forEach(btn =>
    btn.classList.toggle('is-active', btn.dataset.mode === currentSensitiveMode)
  );
  updateSensitiveBar();
  applySensitiveMode(currentSensitiveMode);
}