import '../css/frontend.css';

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
