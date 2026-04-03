# LL Before & After Starter

WordPress plugin starter for the LL Before & After plugin.

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
ln -s "/path/to/ll-bag-starter" "/path/to/wordpress/wp-content/plugins/ll-bag-starter"
```

**3. Activate the plugin**

Go to WP Admin → Plugins and activate **LL Before & After Starter**.

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

Files in the plugin can be overridden from your theme by placing files at the corresponding path under `your-theme/ll-before-after/`.

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

---

## Hooks

The plugin exposes WordPress filters so themes can override specific pieces of markup without copying full templates. All hooks follow the pattern `lifted_logic/bag/{hook_name}`.

### `lifted_logic/bag/bag_back_button_markup`

Overrides the back-to-gallery link on the single post page.

**Parameters passed to the filter:**
| # | Variable | Type | Description |
|---|----------|------|-------------|
| 1 | `$markup` | `string` | Full anchor tag HTML |
| 2 | `$classes` | `string` | CSS classes on the anchor |
| 3 | `$text` | `string` | Link text |
| 4 | `$href` | `string` | Link URL |

```php
add_filter( 'lifted_logic/bag/bag_back_button_markup', function( $markup, $classes, $text, $href ) {
    return '<a href="' . $href . '" class="my-custom-back-link">' . $text . '</a>';
}, 10, 4 );
```

---

### `lifted_logic/bag/related_slider_arrows_markup`

Overrides the previous/next arrow buttons on the related posts slider.

**Parameters passed to the filter:**
| # | Variable | Type | Description |
|---|----------|------|-------------|
| 1 | `$markup` | `string` | Full wrapper `<div>` containing both buttons |
| 2 | `$prev` | `string` | Previous button HTML only |
| 3 | `$next` | `string` | Next button HTML only |

The buttons must keep `splide__arrow--prev` and `splide__arrow--next` classes for Splide.js to wire them up.

```php
add_filter( 'lifted_logic/bag/related_slider_arrows_markup', function( $markup, $prev, $next ) {
    return '
        <div class="my-arrows splide__arrows">
            <button class="my-arrow splide__arrow--prev">←</button>
            <button class="my-arrow splide__arrow--next">→</button>
        </div>
    ';
}, 10, 3 );
```

---

### `lifted_logic/bag/link_card_markup`

Overrides the CTA link card on the single post page sidebar.

**Parameters passed to the filter:**
| # | Variable | Type | Description |
|---|----------|------|-------------|
| 1 | `$markup` | `string` | Full card HTML |
| 2 | `$title` | `string` | Card heading text |
| 3 | `$link` | `array` | ACF link array (`url`, `title`, `target`) |

```php
add_filter( 'lifted_logic/bag/link_card_markup', function( $markup, $title, $link ) {
    return '
        <div class="my-cta-card">
            <p>' . $title . '</p>
            <a href="' . $link['url'] . '">' . $link['title'] . '</a>
        </div>
    ';
}, 10, 3 );
```

---

## Structure

```
ll-bag-starter/
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
