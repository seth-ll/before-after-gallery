import Splide from '@splidejs/splide';

export function initRelatedSlider() {
  const relatedEl = document.querySelector('.ba-related-slider[data-post-id]');
  if (!relatedEl || typeof llBag === 'undefined') return;

  const formData = new FormData();
  formData.append('action',     llBag.relatedAction);
  formData.append('nonce',      llBag.relatedNonce);
  formData.append('exclude_id', relatedEl.dataset.postId);

  fetch(llBag.ajaxUrl, { method: 'POST', body: formData })
    .then(res => res.json())
    .then(data => {
      if (!data.success) return;

      const count = data.data.count ?? 0;

      if (count < 2) {
        relatedEl.closest('.ba-single__related')?.style.setProperty('display', 'none');
        return;
      }

      if (data.data.html) {
        relatedEl.querySelector('.splide__list').innerHTML = data.data.html;
      }

      new Splide(relatedEl, {
        type:       'slide',
        perPage:    2,
        gap:        '32px',
        pagination: false,
        arrows:     count >= 3,
      }).mount();
    })
    .catch(() => {});
}
