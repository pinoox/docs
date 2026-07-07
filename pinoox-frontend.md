# Pinoox frontend dev (Vite + Twig hybrid themes)

Unified local development: **one command** starts PHP + Vite.

```bash
php pinoox dev              # interactive pick
php pinoox dev spark        # manager theme
php pinoox dev welcome      # welcome theme
composer run dev            # same as php pinoox dev
```

Browse the **PHP app URL** in your browser. Twig injects HMR via `vite_tags()`; `dist/hot` is written automatically.

## Commands

| Command | Action |
|---------|--------|
| `php pinoox dev` | PHP serve + Vite HMR |
| `php pinoox fe {theme} dev` | Same (alias `fe`, `frontend`) |
| `php pinoox fe {theme} build` | Production build + WebServerFix sync |
| `php pinoox fe {theme} info` | Stack, hot file, recommended Twig |
| `php pinoox dev --no-serve` | Vite only (MAMP, Docker PHP) |

## Theme layout

```text
apps/{package}/theme/{name}/
├── frontend.config.php   # profile + stack (entry/manifest auto)
├── vite.config.js        # imports ./vite.pinoox.mjs
├── vite.pinoox.mjs       # synced by fe dev/build from pincore / packages
├── partials/scripts.twig # pinoox_bootstrap() + vite_tags('src/main.js')
└── dist/hot              # written by Vite when dev runs (gitignore)
```

## Twig

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/main.js')|raw }}
```

Production: reads `dist/.vite/manifest.json`. Dev: reads `dist/hot` or `VITE_DEV` + `VITE_DEV_SERVER`.

## Environment

### Project root `.env`

```env
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
APP_URL=http://127.0.0.1:8000
```

`VITE_DEV` is a fallback when `dist/hot` is missing. Prefer the hot file (automatic with `php pinoox dev`).

### Theme `.env` (optional)

```env
VITE_SERVER_URL=http://127.0.0.1:8000
```

Set for MAMP/subfolder installs. `php pinoox dev` auto-sets `VITE_SERVER_URL` and `VITE_DEV_PROXY` from app routes when possible.

| Variable | Set by | Purpose |
|----------|--------|---------|
| `PINOOX_HOT_FILE` | CLI | Hot file path (default `dist/hot`) |
| `VITE_DEV_PORT` | theme `.env` | Pin Vite port; omit for auto (5173→5174→…) |
| `VITE_SERVER_URL` | CLI / theme `.env` | PHP backend for API proxy |
| `VITE_DEV_PROXY` | CLI | Comma-separated proxy prefixes |

## Vite plugin

Themes use `vite.pinoox.mjs` (synced locally) or npm `@pinoox/vite-plugin` from `packages/pinoox-vite-plugin`.

```js
import pinooxHot, { pinooxRefresh, pinooxServer } from './vite.pinoox.mjs';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    return {
        plugins: [vue(), pinooxHot(), pinooxRefresh()],
        server: pinooxServer(env),
    };
});
```

- **pinooxHot** — writes `dist/hot` for PHP `ViteHelper`
- **pinooxRefresh** — full reload on Twig/PHP changes
- **pinooxServer** — port, host, API proxy from env

## MAMP workflow

```bash
# Terminal 1 — Vite only
php pinoox dev spark --no-serve

# theme/spark/.env
VITE_SERVER_URL=http://localhost/pinoox/manager
```

Open the MAMP URL in the browser.

## New theme

```bash
php pinoox theme:create my-theme
php pinoox fe com_my_app scaffold --stack=vue
php pinoox fe com_my_app install
php pinoox dev com_my_app
```

## Laravel comparison

| Laravel | Pinoox |
|---------|--------|
| `@vite()` | `vite_tags()` |
| `public/hot` | `theme/dist/hot` |
| `laravel-vite-plugin` | `vite.pinoox.mjs` / `@pinoox/vite-plugin` |
| `composer run dev` | `php pinoox dev` / `composer run dev` |
