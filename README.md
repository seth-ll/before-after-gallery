# LL Before & After

WordPress plugin for the LL Before & After plugin.

## Requirements

- PHP 8.0+
- Node.js
- A local WordPress install

## Setup

**1. Install dependencies**

```bash
composer install
npm install
```

**2. Symlink into your local WordPress install plugin folder**

```bash
ln -s "/path/to/ll-bag" "/path/to/wordpress/wp-content/plugins/ll-bag"
```

**3. Activate the plugin**

Go to WP Admin → Plugins and activate **LL Before & After**.

## Development

Start the Vite dev server with hot module reloading:

```bash
npm run dev
```

## Production

Build assets for production:

```bash
npm run build
```

## Theme Overrides

Files in the plugin can be overridden from your theme by placing files at the corresponding path under `your-theme/ll-before-after/`. The plugin checks the theme location first and falls back to the plugin file — no configuration required.

### Templates

Copy any template from `templates/` into `your-theme/ll-before-after/` and the theme version will be used instead:

```
your-theme/
└── ll-before-after/
    ├── single-ll_before_after.php
    ├── archive-ll_before_after.php
    ├── archive-ll_before_after_category.php
    └── archive-ll_before_after_categories.php
```

### Partials

Any partial included via `TemplateLoader::get()` can also be overridden. Place the file at `your-theme/ll-before-after/partials/{filename}`:

```
your-theme/
└── ll-before-after/
    └── partials/
        ├── before-after-hero-banner.php   # Archive hero banner
        ├── post-card.php                  # Grid card for archive and related slider
        └── filters.php                    # Filter sidebar
```

> **Note:** Partials included via `bag_include_partial()` (e.g. `fit-image`) are hardcoded to the plugin directory and cannot be overridden from the theme.

#### Hero Banner ACF fields

The plugin registers ACF fields for `before-after-hero-banner.php` in **B&A Posts → Settings → Archive Settings**. When a theme overrides that partial, those fields are automatically removed from the admin — the plugin detects the override at registration time and skips them.

**When you override the partial, register your own field group on the same settings page:**

```php
// In your theme's functions.php
add_action( 'acf/init', function () {
    acf_add_local_field_group( [
        'key'    => 'group_my_theme_hero_banner',
        'title'  => 'Hero Banner',
        'fields' => [
            [
                'key'   => 'field_my_theme_hero_heading',
                'label' => 'Hero Heading',
                'name'  => 'my_theme_hero_heading',
                'type'  => 'text',
            ],
            [
                'key'           => 'field_my_theme_hero_link',
                'label'         => 'Hero Link',
                'name'          => 'my_theme_hero_link',
                'type'          => 'link',
                'return_format' => 'array',
            ],
            // add more fields as needed
        ],
        'location' => [
            [
                [
                    'param'    => 'options_page',
                    'operator' => '==',
                    'value'    => 'll-bag-settings',
                ],
            ],
        ],
    ] );
} );
```

Your field group appears as a separate section on the **B&A Posts → Settings** page. Read values in your template with `get_field( 'my_theme_hero_heading', 'option' )`.

To force the plugin's default hero banner fields to register even when your override file is present:

```php
add_filter( 'll_bag/hero_banner_fields_enabled', '__return_true' );
```

### CSS

Override plugin CSS files by placing them at `your-theme/ll-before-after/css/{filename}`:

```
your-theme/
└── ll-before-after/
    └── css/
        └── ba-colors.css   # Override plugin color variables
```

Overrideable CSS files:

| File | Purpose |
|------|---------|
| `ba-colors.css` | CSS custom properties for UI colors |

### Header & Footer

By default the archive templates call `get_header()` and `get_footer()`. Use these filters to swap the template name or skip them entirely:

```php
// Load a named variant — calls get_header('minimal') which loads header-minimal.php
add_filter( 'll_bag/header_template', fn() => 'minimal' );
add_filter( 'll_bag/footer_template', fn() => 'minimal' );

// Skip header and/or footer completely
add_filter( 'll_bag/header_template', fn() => false );
add_filter( 'll_bag/footer_template', fn() => false );
```

Returning `''` (the default) calls `get_header()`/`get_footer()` with no argument — standard theme behavior.

---

## Hooks

The plugin exposes WordPress filters so themes can override specific pieces of markup without copying full templates. Each hook is a static method on `Hooks` — search the codebase for the method name to find its definition and usage.

---

### `bag_back_button_markup`

Filter: `lifted_logic/bag/bag_back_button_markup`

Overrides the back-to-gallery link at the top of the single post sidebar. The `$href` defaults to the post type archive URL, falling back to `site_url('/')` if no archive is configured. If a `ba_ref` query param is present it is used instead (preserves filtered archive state).

**Default markup:**

```html
<a href="{archive-url}" class="bag_back-text bag-inline-block">Back to Gallery</a>
```

**Parameters passed to the filter:**
| # | Variable | Type | Description |
|---|----------|------|-------------|
| 1 | `$markup` | `string` | Full anchor tag HTML |
| 2 | `$classes` | `string` | CSS classes on the anchor |
| 3 | `$text` | `string` | Link text |
| 4 | `$href` | `string` | Link URL |

The example below reproduces the plugin default exactly — copy, paste into your theme, then modify:

```php
add_filter( 'lifted_logic/bag/bag_back_button_markup', function( $markup, $classes, $text, $href ) {
    return '<a href="' . $href . '" class="' . $classes . '">' . $text . '</a>';
}, 10, 4 );
```

---

### `bag_related_slider_arrows_markup`

Filter: `lifted_logic/bag/related_slider_arrows_markup`

Overrides the previous/next arrow buttons on the related posts slider. The buttons **must keep** `splide__arrow--prev` and `splide__arrow--next` classes — Splide.js uses these to wire up navigation.

The filter receives `$prev` and `$next` as separate strings so you can replace only one button while keeping the other, or rearrange them within a custom wrapper.

**Default markup:**

```html
<div class="ll-ba-single__related-arrows splide__arrows">
  <button class="ll-ba-single__related-arrow ll-ba-single__related-arrow--prev splide__arrow--prev">
    <svg class="ll-ba-single__related-arrow-icon icon icon-arrow-right" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
    <span class="sr-only">Previous Slide</span>
  </button>
  <button class="ll-ba-single__related-arrow ll-ba-single__related-arrow--next splide__arrow--next">
    <svg class="ll-ba-single__related-arrow-icon icon icon-arrow-right" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
    <span class="sr-only">Next Slide</span>
  </button>
</div>
```

**Parameters passed to the filter:**
| # | Variable | Type | Description |
|---|----------|------|-------------|
| 1 | `$markup` | `string` | Full wrapper `<div>` containing both buttons |
| 2 | `$prev` | `string` | Previous button HTML only |
| 3 | `$next` | `string` | Next button HTML only |

The example below reproduces the plugin default exactly — copy, paste into your theme, then modify:

```php
add_filter( 'lifted_logic/bag/related_slider_arrows_markup', function( $markup, $prev, $next ) {
    return '
      <div class="ll-ba-single__related-arrows splide__arrows">
        <button class="ll-ba-single__related-arrow ll-ba-single__related-arrow--prev splide__arrow--prev">
          <svg class="ll-ba-single__related-arrow-icon icon icon-arrow-right" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
          <span class="sr-only">Previous Slide</span>
        </button>
        <button class="ll-ba-single__related-arrow ll-ba-single__related-arrow--next splide__arrow--next">
          <svg class="ll-ba-single__related-arrow-icon icon icon-arrow-right" aria-hidden="true"><use xlink:href="#icon-arrow-right"></use></svg>
          <span class="sr-only">Next Slide</span>
        </button>
      </div>
    ';
}, 10, 3 );
```

---

### `bag_link_card_markup`

Filter: `lifted_logic/bag/link_card_markup`

Overrides the CTA link card in the single post sidebar. The card only renders when a CTA link is configured on the **B&A Posts → Settings** options page (Global Single Page Options tab) — it is skipped entirely when the link field is empty.

`$title` and `$link` come from the `ll_ba_global_cta_title` and `ll_ba_global_cta_link` ACF options fields.

**Default markup:**

```html
<div class="ll-ba-single__cta-card">
  <p class="ll-ba-single__cta-title ba_hdg-small">{title}</p>
  <a class="ll-ba-single__cta-button ba_btn-primary" href="{url}">{link title}</a>
</div>
```

**Parameters passed to the filter:**
| # | Variable | Type | Description |
|---|----------|------|-------------|
| 1 | `$markup` | `string` | Full card HTML |
| 2 | `$title` | `string` | Card heading text (from options page) |
| 3 | `$link` | `array` | ACF link array — keys: `url`, `title`, `target` |

The example below reproduces the plugin default exactly — copy, paste into your theme, then modify:

```php
add_filter( 'lifted_logic/bag/link_card_markup', function( $markup, $title, $link ) {
    $href      = $link['url'] ?? '';
    $link_text = $link['title'] ?? '';
    $target    = $link['target'] ? 'target="' . $link['target'] . '"' : '';
    $sr_text   = $link['target'] === '_blank' ? '<span class="sr-only"> (opens in new tab)</span>' : '';

    return '
      <div class="ll-ba-single__cta-card">
        <p class="ll-ba-single__cta-title ba_hdg-small">' . $title . '</p>
        <a class="ll-ba-single__cta-button ba_btn-primary" href="' . $href . '" ' . $target . '>' . $link_text . ' ' . $sr_text . '</a>
      </div>
    ';
}, 10, 3 );
```

---

## Structure

```
ll-bag/
├── src/
│   ├── Plugin.php                    # Boots container, registers hooks
│   ├── PostType/
│   │   └── BeforeAfterPostType.php   # CPT registration
│   ├── Admin/
│   │   └── AdminMenu.php             # Asset enqueuing
│   └── Support/
│       └── Vite.php                  # HMR + manifest helper
└── resources/
    ├── js/admin.js                   # Admin JS entry point
    └── css/admin.css                 # Admin CSS entry point (Tailwind)
```
