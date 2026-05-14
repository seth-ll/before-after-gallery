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

### Theme Component Injection (`src/Integration/ThemeComponentInjector.php`)

Plugin components can be injected into the LL theme's flexible content field so they appear in the "Add Component" dropdown alongside native theme components. This works on both newer sites (PHP `ComponentProvider`) and older sites (JSON/DB-based ACF) because both use the same FC field key.

**How it works — three hooks per component:**

1. **`acf/load_field`** (`injectLayouts`) — intercepts when ACF loads the FC field (`field_5d0d37adc1475`) and appends the plugin's layout to `$field['layouts']`. This makes the layout appear in the admin dropdown.

2. **`{component-slug}_files`** (`injectRelatedBnaTemplate`) — intercepts the theme's `ll_include_component()` file search. Computes a relative path from the theme directory to the plugin's template using `..` traversal (PHP `file_exists()` resolves `..` at the OS level), so `locate_template()` finds the plugin file without needing theme changes.

3. **`lifted_logic/component/format_data/{layout_name}`** (`formatRelatedBnaData`) — the theme's `ll_format_component_data()` only passes through sub-fields whose key starts with `{layout_name}_`. This filter receives the raw `$data` array and manually maps field values to `$new_data`. Without this, `$component_data` arrives empty in the template.

**Critical layout definition rules** (older ACF Pro versions are strict):
- Every layout must include `'_name'`, `'display'`, `'layout'`, `'min'`, `'max'`
- Every sub_field must include both `'name'` and `'_name'` (set to the same value)
- Sub-field names must follow `{layout_name}_{field_name}` convention so `ll_format_component_data` strips the prefix and delivers them as `$component_data['{field_name}']`
- ACF's `ll_format_component_data` is NOT used for field delivery — the `format_data` filter handles this directly

**Disabling components (must be in `functions.php`, checked on `after_setup_theme`):**

```php
// Disable one component
add_filter( 'll_bag/register_component/ll_ba_slider', '__return_false' );
add_filter( 'll_bag/register_component/ll_ba_grid',   '__return_false' );
add_filter( 'll_bag/register_component/ll_ba_related_bna', '__return_false' );

// Disable all components (master switch)
add_filter( 'll_bag/register_components', '__return_false' );
```

Disabling a component removes it from `injectLayouts()`, `maybeRegisterHooks()`, and `registerLocalFields()` — layout, template serving, format_data filter, and AJAX field are all gated by the same filter check.

**Current components** (all in `ThemeComponentInjector`):

| Layout name | Label | Slug (for file filter) |
|---|---|---|
| `ll_ba_related_bna` | Related Before & Afters | `ll-ba-related-bna` |
| `ll_ba_grid` | Before & Afters Grid | `ll-ba-grid` |
| `ll_ba_slider` | Before & After Slider | `ll-ba-slider` |

**Adding a new plugin component:**

1. Create `components/{ComponentName}/` with `.php`, `.css`, `.js` files
2. Import CSS and JS in `frontend.js` under `// Components`
3. Add a `private function {name}Layout(): array` to `ThemeComponentInjector` with all required keys
4. Call it in `injectLayouts()` alongside the existing layouts
5. Add a `{component-slug}_files` filter + inject method for the template
6. Add a `lifted_logic/component/format_data/{layout_name}` filter + format method for data mapping
7. If the component has a relationship field, also register it via `acf_add_local_field()` in `registerLocalFields()` — the AJAX handler needs this to find the field config when populating the selector
8. In the template, read fields via `$component_data['{field_name}']` — NOT `get_sub_field()`

**Why not `get_sub_field()`:** The theme uses `foreach (get_field('components'))`, not `have_rows()`. There is no ACF row context active when the template is included. All data arrives through `$component_data`.

**`ba_grid-cols-container` gotcha:** This theme class creates a 3-column grid (left bleed / content / right bleed). Every direct child that should be in the content column must have `grid-column: 2 / 3` in its CSS. Without it, the element is auto-placed into a bleed column and becomes invisible even though the HTML is correct and JS runs. This applies to pagination containers, sensitive image bars, and all sibling elements inside this wrapper.

**Client-side pagination:** Use `renderPagination(el, totalPages, currentPage, callback)` from `pagination.js`. Count `.ll-ba-card` elements with `querySelectorAll`, compute `Math.ceil(count / PAGE_SIZE)`, and pass a `showPage` function as the callback. The pagination container must have `grid-column: 2 / 3` to be visible inside `ba_grid-cols-container`. See `components/BeforeAndAftersGrid/before-and-afters-grid.js` for the reference implementation.

**Relationship fields in components:** The ACF relationship AJAX handler calls `acf_get_field($key)` to get the field config. If the field is only defined inside a layout's `sub_fields` (in memory via `acf/load_field`), it won't be found and the dropdown returns empty. Register it separately via `acf_add_local_field()` in `registerLocalFields()` to fix this.

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
