# LL Before & After Starter

WordPress plugin starter for the LL Before & After plugin. Includes a registered `ll_before_after` custom post type, Illuminate components (container, support, database), Vite with hot module reloading, and Tailwind CSS v3.

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

**2. Symlink into your local WordPress install**

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
