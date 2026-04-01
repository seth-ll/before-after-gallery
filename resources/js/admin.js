import '../css/admin.css';

/**
 * Convert a human label to a snake_case meta key with ll_ba_ prefix.
 * e.g. "Body Area" → "ll_ba_body_area"
 *      "Surgical / Non-Surgical" → "ll_ba_surgical_non_surgical"
 */
function labelToMetaKey(label) {
  return 'll_ba_' + label
    .toLowerCase()
    .replace(/[^a-z0-9]+/g, '_')
    .replace(/^_+|_+$/g, '');
}

document.addEventListener('DOMContentLoaded', () => {
  const tbody  = document.getElementById('ll-bag-filter-tbody');
  const addBtn = document.getElementById('ll-bag-add-filter');

  if (!tbody || !addBtn) return;

  // ── Drag-and-drop row reordering ───────────────────────────────────────────

  let draggedRow = null;

  tbody.addEventListener('dragstart', (e) => {
    draggedRow = e.target.closest('tr');
    setTimeout(() => draggedRow?.classList.add('opacity-50'), 0);
  });

  tbody.addEventListener('dragend', () => {
    draggedRow?.classList.remove('opacity-50');
    draggedRow = null;
  });

  tbody.addEventListener('dragover', (e) => {
    e.preventDefault();
    const target = e.target.closest('tr');
    if (!target || target === draggedRow) return;
    const rect = target.getBoundingClientRect();
    tbody.insertBefore(draggedRow, e.clientY < rect.top + rect.height / 2 ? target : target.nextSibling);
  });

  // ── Toggle searchable option visibility based on display type
  tbody.addEventListener('change', (e) => {
    const select = e.target.closest('.ll-bag-display-select');
    if (!select) return;

    const row = select.closest('.ll-bag-filter-row');
    const searchableWrap = row?.querySelector('.ll-bag-searchable-wrap');

    if (searchableWrap) {
      searchableWrap.style.display = select.value === 'checkbox' ? 'flex' : 'none';
    }
    // Uncheck searchable when switching away from checkbox
    if (select.value !== 'checkbox') {
      const searchableInput = row?.querySelector('.ll-bag-searchable-input');
      if (searchableInput) searchableInput.checked = false;
    }
  });

  // REMOVE FILTER ROW
  tbody.addEventListener('click', (e) => {
    const btn = e.target.closest('.ll-bag-remove-filter');
    if (!btn) return;

    btn.closest('.ll-bag-filter-row')?.remove();
  });

  tbody.addEventListener('input', (e) => {
    const labelInput = e.target.closest('.ll-bag-label-input');
    if (!labelInput) return;

    const row = labelInput.closest('.ll-bag-filter-row');
    // skip auto-generating the meta key if it's not a new row
    if (!row?.dataset.isNew) return;

    const metaKeyInput = row.querySelector('.ll-bag-meta-key-input');
    const metaKeyHint  = row.querySelector('.ll-bag-meta-key-hint');
    const key = labelToMetaKey(labelInput.value);

    if (metaKeyInput) metaKeyInput.value = key;
    if (metaKeyHint)  metaKeyHint.textContent = key;
  });

  // NEW FILTER ROW: clone template from filter-settings.php
  addBtn.addEventListener('click', () => {
    const template = document.getElementById('ll-bag-filter-template');
    if (!template) return;

    const id    = `f${Date.now()}`;
    const clone = template.content.cloneNode(true);
    const row   = clone.querySelector('tr');
    if (!row) return;

    row.innerHTML  = row.innerHTML.replaceAll('__ID__', id);
    row.dataset.id  = id;
    row.dataset.isNew = '1'; // flag so meta key auto-generates from label

    tbody.appendChild(row);
  });
});
