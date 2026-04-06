export function initCardLinks() {
  document.addEventListener('mousedown', e => {
    const link = e.target.closest('.ll-ba-card__link');
    if (!link) return;

    // Pass through existing ba_ref (if on a single post), or use current archive URL
    const currentParams = new URLSearchParams(window.location.search);
    const backRef = currentParams.get('ba_ref') || window.location.href;

    const dest = new URL(link.href);
    dest.searchParams.set('ba_ref', backRef);
    link.href = dest.toString();
  });
}
