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
