# @pinooxhq/vite-plugin

[← Back to index](../README.md)

**[@pinooxhq/vite-plugin](https://www.npmjs.com/package/@pinooxhq/vite-plugin)** connects a Pinoox theme folder to PHP: dev state file, dev proxy, Twig/PHP refresh, and production manifest for `vite_tags()`.

Install it in every Vite theme (`apps/{package}/theme/{theme}/`). The Pinoox CLI (`php pinoox fe dev`, `pinx fe:dev`) expects this package — not the legacy synced `vite.pinoox.mjs` stub.

---

## How it works

| Server | Default | Role |
|--------|---------|------|
| PHP | `:8000` | Twig shell, routes, API |
| Vite | `:5173` | JS/CSS with HMR |

```
Browser → PHP (Twig + vite_tags)
              ↓
         .pinoox/dev.json active + HMR mode?
         yes → Vite client + entries from Vite origin
         no  → hashed assets from dist/.vite/manifest.json
```

`pinoox()` in `vite.config.js`:

1. Writes **`.pinoox/dev.json`** so PHP enables HMR (replaces legacy `dist/hot`).
2. Proxies app routes to PHP (`VITE_DEV_PROXY`).
3. Full-reloads when **Twig** or **app PHP** changes.
4. Sets **build entries** and **manifest** for production.

**Always open the PHP app URL** printed by `fe dev` — not `http://127.0.0.1:5173` directly.

See [Frontend & Vite](./frontend-vite.md) for CLI commands, env vars, and multi-app dev.

---

## Install

In the theme folder:

```bash
npm install -D @pinooxhq/vite-plugin vite
```

Or from the Pinoox project root:

```bash
php pinoox fe install com_my_shop --theme=default
php pinoox fe spark install                 # theme folder shorthand
```

`fe install` / `fe dev` can sync the plugin version expected by your Pinoox release. Use `--fix-vite` to patch an old `vite.config.js` that still imports `vite.pinoox.mjs`.

**`package.json` scripts:**

```json
{
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "watch": "vite build --watch"
  }
}
```

---

## Minimal `vite.config.js`

### Vanilla JavaScript

```js
import { defineConfig } from 'vite';
import pinoox from '@pinooxhq/vite-plugin';

export default defineConfig({
    plugins: [
        pinoox(['src/main.js']),
    ],
});
```

### Vue

```js
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import pinoox from '@pinooxhq/vite-plugin';
import { pinooxVueTemplateOptions } from '@pinooxhq/vite-plugin/vue';

export default defineConfig({
    plugins: [
        pinoox(['src/main.js']),
        vue(pinooxVueTemplateOptions()),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    },
});
```

`pinooxVueTemplateOptions()` fixes asset URLs in Vue SFC templates when HTML is served from PHP and assets from Vite (different origins in dev).

### React

```js
import { fileURLToPath, URL } from 'node:url';
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import pinoox from '@pinooxhq/vite-plugin';

export default defineConfig({
    plugins: [
        pinoox(['src/main.jsx']),
        react(),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./src', import.meta.url)),
        },
    },
});
```

Do **not** import `@pinooxhq/vite-plugin/vue` in React themes.

### Multiple entries (JS + CSS)

```js
pinoox([
    'src/main.js',
    'src/assets/styles/app-view-error.scss',
])
```

Twig must list every entry:

```twig
{{ vite_tags(['src/main.js', 'src/assets/styles/app-view-error.scss'])|raw }}
```

---

## `pinoox()` API

```js
// one entry
pinoox('src/main.js')

// multiple entries
pinoox(['src/main.js', 'src/assets/styles/app.css'])

// full config
pinoox({
    entries: ['src/main.js'],
    refresh: true,              // true | false | string[] (Twig globs)
    hotFile: '.pinoox/dev.json',
    env: { VITE_DEV_PORT: '5174' },
    build: { rollupOptions: { /* merged */ } },
    server: { /* merged */ },
})
```

| Feature | Description |
|---------|-------------|
| Build entries | `build.rollupOptions.input` from your paths |
| Manifest | `build.manifest: true` → `dist/.vite/manifest.json` |
| Dev state | Writes `.pinoox/dev.json` for PHP HMR (legacy: `dist/hot`) |
| Dev proxy | Forwards app routes to PHP (`VITE_SERVER_URL`, `VITE_DEV_PROXY`) |
| Twig refresh | Full reload when theme `*.twig` changes |
| PHP refresh | Full reload when `VITE_DEV_REFRESH` paths change (from `fe dev`) |
| Dev assets | Rewrites `/src/`, `/node_modules/`, … to Vite origin |

Add framework plugins (Vue, React, Tailwind, …) **after** `pinoox()` in the `plugins` array.

---

## Package exports

| Import | Purpose |
|--------|---------|
| `@pinooxhq/vite-plugin` | Default `pinoox()` |
| `@pinooxhq/vite-plugin/vue` | `pinooxVueTemplateOptions()`, optional `vue()` wrapper |

---

## Twig integration

| Piece | Role |
|-------|------|
| `pinoox_bootstrap()` | `window.__PINOOX__` — URLs, locale, page props |
| `vite_tags('src/main.js')` | HMR scripts in dev; hashed tags in production |
| `vite_asset('src/logo.png')` | Versioned static URL from manifest |
| `frontend.config.php` | Stack, entry, manifest path, dev port |

Example `partials/scripts.twig`:

```twig
{{ pinoox_bootstrap(bootstrap|default({}))|raw }}
{{ vite_tags('src/main.js')|raw }}
```

---

## Development workflow

```bash
# One command — PHP + Vite + env (recommended)
php pinoox fe spark dev
# or shortcut:
php pinoox dev spark

# Platform — multiple apps
php pinoox fe dev:apps

# Manual — two terminals
php pinoox serve --app=com_my_shop@/
cd apps/com_my_shop/theme/default && npm run dev
```

`fe dev` waits until Vite is ready, then prints the PHP URL. Edit Twig → full reload. Edit JS/CSS → HMR.

---

## Production build

```bash
php pinoox fe build com_my_shop
# or:
cd apps/com_my_shop/theme/default && npm run build
```

Output: `dist/.vite/manifest.json` and hashed assets under `dist/assets/`. No active `.pinoox/dev.json` — PHP uses the manifest only.

---

## Legacy `vite.pinoox.mjs`

Older themes imported `pinooxHot`, `pinooxServer`, and `pinooxRefresh` from a synced `vite.pinoox.mjs` file. New themes should use the npm package:

```bash
npm install -D @pinooxhq/vite-plugin
php pinoox fe spark dev --fix-vite
```

The low-level exports (`pinooxHot`, `pinooxServer`, …) remain available from `@pinooxhq/vite-plugin` for advanced setups.

---

## Related docs

- [Frontend & Vite](./frontend-vite.md) — CLI, env vars, HMR vs manifest
- [Twig templates](./templates.md)
- [Pinx CLI — frontend commands](../start/pinx-cli.md)
- [npm package README](https://github.com/pinoox/vite-plugin)

---

[← Back to index](../README.md)
