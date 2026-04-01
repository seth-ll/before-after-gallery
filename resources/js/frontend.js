import '../css/frontend.css';
import '../css/theme-before-after.css';

document.addEventListener('DOMContentLoaded', () => {
  const filtersEl = document.getElementById('ll-ba-filters');
  const grid      = document.getElementById('ll-ba-grid');

  if (!filtersEl || !grid) return;

  // ── Accordion toggles ──────────────────────────────────────────────────────

  filtersEl.addEventListener('click', (e) => {
    const toggle = e.target.closest('.ll-ba-filter-toggle');
    if (!toggle) return;

    const group   = toggle.closest('.ll-ba-filter-group');
    const content = group?.querySelector('.ll-ba-filter-content');
    const arrow   = toggle.querySelector('.ll-ba-filter-arrow');
    if (!content) return;

    const isOpen = !content.classList.contains('hidden');
    content.classList.toggle('hidden', isOpen);
    toggle.setAttribute('aria-expanded', String(!isOpen));
    arrow?.classList.toggle('rotate-180', !isOpen);
  });

  // ── Option search (client-side, filters visible checkboxes only) ───────────

  filtersEl.addEventListener('input', (e) => {
    const search = e.target.closest('.ll-ba-option-search');
    if (!search) return;

    const term    = search.value.toLowerCase();
    const list    = search.closest('.ll-ba-filter-content')?.querySelector('.ll-ba-checkbox-list');
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

  // ── Active tag: remove individual filter ──────────────────────────────────

  document.getElementById('ll-ba-active-tags')?.addEventListener('click', (e) => {
    const btn = e.target.closest('.ll-ba-tag-remove');
    if (!btn) return;

    const { metaKey, value } = btn.dataset;
    const group = filtersEl.querySelector(`[data-meta-key="${metaKey}"]`);
    if (!group) return;

    if (group.dataset.display === 'checkbox') {
      const cb = [...group.querySelectorAll('.ll-ba-checkbox-filter')]
        .find(el => el.value === value);
      if (cb) cb.checked = false;
    } else {
      const sel = group.querySelector('.ll-ba-dropdown-filter');
      if (sel) sel.value = '';
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
        const checked = [...group.querySelectorAll('.ll-ba-checkbox-filter:checked')]
          .map(el => el.value);
        if (checked.length) active[key] = checked;
      } else {
        const val = group.querySelector('.ll-ba-dropdown-filter')?.value ?? '';
        if (val) active[key] = val;
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

    grid.style.opacity = '0.5';

    try {
      const res  = await fetch(llBag.ajaxUrl, { method: 'POST', body: formData });
      const data = await res.json();

      if (data.success) {
        grid.innerHTML = data.data.html || `<p class="col-span-full py-12 text-sm text-center text-gray-500">${llBag.noResults ?? 'No results found.'}</p>`;
      }
    } finally {
      grid.style.opacity = '';
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
        const cb = group?.querySelector(`.ll-ba-checkbox-filter[value="${v}"]`);
        const displayName = cb?.dataset.termName ?? v;

        const tag = document.createElement('span');
        tag.className = 'll-ba-tag inline-flex items-center gap-1 rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-700';
        tag.innerHTML = `${escHtml(label)}: ${escHtml(displayName)} <button type="button" class="ll-ba-tag-remove ml-1 hover:text-black" data-meta-key="${escAttr(key)}" data-value="${escAttr(v)}" aria-label="Remove filter">&times;</button>`;
        tags.appendChild(tag);
      });
    }

    bar.classList.toggle('hidden', tags.children.length === 0);
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
        const cb = [...group.querySelectorAll('.ll-ba-checkbox-filter')]
          .find(el => el.value === value);
        if (cb) cb.checked = true;
      } else {
        const sel = group.querySelector('.ll-ba-dropdown-filter');
        if (sel) sel.value = value;
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
});
