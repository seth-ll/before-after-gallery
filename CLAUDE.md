# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
npm run dev       # Vite dev server with HMR (writes a `hot` file; Vite::enqueueFrontendAssets() detects it)
npm run build     # Production build to public/build/ with manifest
composer install  # PHP autoloader (PSR-4 via illuminate/container)
```

There are no test commands. No linter is configured.

---

## Architecture Overview

### Plugin Entry Point

`ll-bag-starter.php` defines four constants (`LL_BAG_VERSION`, `LL_BAG_FILE`, `LL_BAG_PATH`, `LL_BAG_URL`), requires `vendor/autoload.php`, then **directly requires** `src/Hooks/functions.php` before class instantiation. This is intentional — global helper functions must exist before any class is resolved from the container.

### Service Container

`src/Plugin.php` uses `illuminate/container` as a lightweight IoC container. All services are registered as singletons. `boot()` calls `->register()` on each service, which is where WordPress `add_action`/`add_filter` calls happen.

### Two-File Hook Pattern

Markup-level WordPress filters live in **two files with different scopes**:

- `src/Hooks/Hooks.php` — namespaced class with static methods. Each method builds markup and passes it through `apply_filters('lifted_logic/bag/{hook_name}', ...)`. Used in templates as `Hooks::method_name()`.
- `src/Hooks/functions.php` — **no namespace declaration**. Contains `bag_include_partial()` and any other global helper functions. This file is required directly from the plugin entry point, not autoloaded.

**Never define global functions in `Hooks.php`** — the namespace makes them inaccessible globally.

### Template Loading

`src/Frontend/TemplateLoader.php` intercepts `template_include` to serve plugin templates. All template resolution checks the active theme first:

```
{theme}/ll-before-after/{file}   ← theme override wins
templates/{file}                  ← plugin fallback
```

Use `TemplateLoader::get('partials/post-card.php', ['post' => $post])` to include a partial with variables extracted into scope. Use `TemplateLoader::render()` when you need the output as a string (e.g. in AJAX handlers). **Do not use `bag_include_partial()` for partials that should be theme-overridable** — it is hardcoded to the plugin directory and bypasses theme resolution.

### ACF Fields

All ACF field groups are registered programmatically via `acf_add_local_field_group()` in two places:

- `src/PostType/Fields.php` — fields on the `ll_before_after` post type and on taxonomy terms
- `src/Admin/SettingsPage.php` — fields on the options page (`ll-bag-settings`)

The images repeater field (`ll_ba_images`) is the core data structure for the single post. Each row has:
- `ll_ba_image_options` — `one-image` | `two-images` | `video`
- `ll_ba_image_ratio` — `wide` | `square` | `panorama` | `vertical`
- `ll_ba_single_image` — image ID (one-image + video)
- `ll_ba_before_image` / `ll_ba_after_image` — image IDs (two-images)
- `ll_ba_comparison_slider` — bool (two-images only)
- `ll_ba_video_url` / `ll_ba_video_title` — video fields

Templates map `ll_ba_image_ratio` values to CSS modifier classes (`ll-ba-single__ratio--wide`, etc.). These classes are defined at the top of `single-post.css` outside any nesting block so they work globally (archive cards and the single page gallery both use them).

### Filter System

`src/Filters/FilterManager.php` manages the archive filter sidebar. Filters are stored as a serialized option (`ll_bag_filters`). The "Card Display" taxonomy — the one whose terms appear as pills on post cards — is stored separately in `ll_bag_card_taxonomy` and read via `FilterManager::getCardTaxonomy()`. `PostTerms::forCard($postId)` is the standard way to fetch pill data in templates.

The AJAX filter handler (`src/Frontend/AjaxHandler.php`) handles two actions:
- `ll_bag_filter` — archive grid filtering; returns rendered `post-card.php` HTML
- `ll_bag_related` — related posts for the single page slider; runs three query passes (card taxonomy → override terms → recent fallback)

### CSS Architecture

No CSS framework. Plain CSS only — no `theme()` calls, no utility classes, no `@apply`.

CSS is split into:
- `resources/css/frontend.css` — entry point; imports partials
- `resources/css/partials/single-post.css` — BEM styles for the single post page
- `resources/css/partials/archive.css` — BEM styles for the archive, post card, and filter sidebar
- `resources/css/partials/hero-banner.css` — BEM styles for the archive hero banner component
- `resources/css/ba-colors.css` — CSS custom properties for UI colors (overrideable from theme)

When adding a new component partial, create a matching CSS file in `resources/css/partials/` and import it in `frontend.css`.

Colors come from CSS custom properties defined in `ba-colors.css` (e.g. `var(--background-fill)`, `var(--text-heading)`). Use `var(--property-name)` directly — there is no token abstraction layer.

CSS nesting is acceptable. Use it for component-scoped child selectors (e.g. `.ll-ba-card { .ll-ba-card__image { ... } }`). Avoid deep nesting — keep selectors readable.

State toggling classes (`ll-ba-hidden`, `rotate-180`, `is-filtering`) are defined in `archive.css` and toggled by JavaScript. Use `ll-ba-hidden` (not `hidden`) for any show/hide state in plugin-owned elements to avoid polluting the global namespace.

### JavaScript

`resources/js/frontend.js` is the main entry point. It initializes:
- Header height CSS variable (`--ba-header-height`, accounts for `#wpadminbar`)
- Primary + nav Splide sliders (synced)
- Related posts Splide slider
- Comparison slider drag interaction
- Magnific Popup for the single post "Read More" modal

Other JS modules are imported from separate files:

| File | Purpose |
|---|---|
| `card.js` | Card link `ba_ref` tracking |
| `filters.js` | Archive filter sidebar, AJAX filtering, active tags, pagination |
| `pagination.js` | Pagination rendering helper |
| `related-posts.js` | AJAX-loaded related posts slider on single page |
| `sensitive.js` | Shared sensitive image helpers — `getSensitiveMode()`, `setSensitiveMode()`, `applySensitiveMode(container, mode)`, `updateSensitiveBar(bar, container)` |
| `cookieUtil.js` | `CookieUtil.getCookie(name)` / `setCookie(name, value, days)` — used by `sensitive.js` |
| `vendor/easy-toggle-state.js` | Declarative toggle library; activated via `data-toggle-*` attributes |

The `llBag` global (set via `wp_localize_script`) provides `ajaxUrl`, `nonce`, `action`, `relatedAction`, `relatedNonce`.

**jQuery plugins** are enqueued via WordPress (`wp_enqueue_script` with `['jquery']` dependency), not bundled through Vite. This avoids CJS/ESM interop issues with jQuery. Current plugins: Magnific Popup (`ll-bag-magnific-popup`, loaded in `TemplateLoader::enqueueMagnificPopup()`). Do not import jQuery-dependent libraries directly in `frontend.js`.

**Sensitive image preference** is stored in the `ll-ba-sensitive-mode` cookie (default: `'blur'`). Always use `getSensitiveMode()` / `setSensitiveMode()` from `sensitive.js` — never read or write `localStorage` or a cookie directly for this value.

### Dynamic CSS Variables

`TemplateLoader::enqueueCssOverrides()` enqueues `ba-colors.css` (checking theme override first), then outputs per-site values as inline CSS via `wp_add_inline_style`. When adding a new ACF color/style option from the settings page, register the field in `SettingsPage.php`, read it in `enqueueCssOverrides()`, and output it as a CSS variable on `:root`. Consume it in CSS via `var(--variable-name)`.

---

## Key Conventions

**Adding a new markup filter hook:** Add a `public static function` to `src/Hooks/Hooks.php` that builds `$markup` and returns `apply_filters('lifted_logic/bag/{name}', $markup, ...$parts)`. Call it as `Hooks::method_name()` in the template. Document it in `README.md` under the Hooks section.

**Adding a global helper function:** Add it to `src/Hooks/functions.php` with a `function_exists` guard. Never add global functions to `Hooks.php`.

**Adding a new partial:** Create the file in `templates/partials/`. Include it via `TemplateLoader::get('partials/my-partial.php', $data)` (theme-overridable) or `bag_include_partial('my-partial', $data)` (plugin-only, no theme override). Add the override path to the header docblock.

**BEM class naming in CSS:** All plugin BEM classes use the `ll-ba-` prefix — no exceptions. Examples: `ll-ba-card`, `ll-ba-single`, `ll-ba-single__gallery`, `ll-ba-comparison-slider`. Do not use the bare `ba-` prefix for plugin classes. Do not use bare utility class names (like `hidden`) that could conflict with theme styles — use `ll-ba-hidden` instead.

**What does NOT get the `ll-ba-` prefix:**
- Theme typography/button classes (`ba_hdg-medium`, `ba_btn-primary`) — these use `ba_` with an underscore and belong to the theme, not the plugin
- Splide library classes (`splide__arrow--prev`, `splide__arrows`, etc.)
- Third-party or WordPress classes (`wysiwyg`, `sr-only`, `js-init-video`)
