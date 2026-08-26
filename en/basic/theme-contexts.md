# Theme contexts

[← Back to index](../README.md)

**Theme contexts are optional.** Most apps use a single theme (`'theme' => 'default'`) and never define `theme-contexts`. You only need this feature when one package must run **several independent UI environments** (for example public site + admin panel), each with its own theme tree, URL prefix, and optional client auth settings.

If you skip `theme-contexts`, nothing else changes — routing, views, and bootstrap work as before.

This is different from **theme inheritance** (`extends` in `theme.php` / `theme-extends`), which layers templates *inside* one active theme. A context chooses **which theme tree** is active for the current request.

---

## When to use (and when not to)

| Use theme contexts when… | Skip them when… |
|--------------------------|-----------------|
| Site and panel share one app but need different themes | The app has a single UI |
| Panel lives under a prefix (`/panel`) with its own login URL | You only need template inheritance (`extends`) |
| Client bootstrap must expose different `loginUrl` / `url.BASE` per area | Routes are JSON API only (no HTML theme) |

---

## Minimal single-theme app (no contexts)

```php
// app.php — enough for most apps
return [
    'package' => 'com_my_shop',
    'theme' => 'default',
];
```

No `theme-contexts`, no `theme.*` flows, no `collection(context: …)`.

---

## Quick setup (optional multi-environment)

### 1. Define contexts in `app.php`

```php
return [
    'package' => 'com_my_shop',

    // Default context when no theme flow ran on the request
    'theme-context' => 'site',

    'theme-contexts' => [
        'site' => [
            'path' => '',                    // optional; empty = app root
            'theme' => 'default',
            'extends' => ['base'],
            'auth' => [                      // optional client overlay
                'client' => ['loginUrl' => '/login'],
            ],
        ],
        'panel' => [
            'path' => 'panel',               // URL prefix + client BASE
            'theme' => 'admin',
            'extends' => ['admin-base'],
            'auth' => [
                'client' => ['loginUrl' => '/panel/auth/login'],
            ],
            'frontend' => [                  // optional Vite overrides
                'stack' => 'vue',
                'entry' => 'src/main.js',
            ],
        ],
    ],

    'alias' => array_merge([
        'auth' => AuthFlow::class,
    ], theme_flow_aliases(['site', 'panel'])),
];
```

Folder layout:

```text
apps/com_my_shop/theme/
├── default/     # site
├── admin/       # panel
├── base/
└── admin-base/
```

### 2. Attach context to routes

**Recommended** — pass `context`; path and `theme.*` flow are filled from `theme-contexts`:

```php
// routes/web.php
use function Pinoox\Router\{collection, get};

collection(context: 'site', routes: __DIR__ . '/site.php');

collection(context: 'panel', routes: __DIR__ . '/panel.php', flows: ['auth']);
```

What the core does automatically when `context` is set:

1. Adds flow `theme.{context}` if it is not already in `flows`
2. If `path` is empty, uses `theme-contexts[context]['path']`
3. Throws if the context name is unknown (when `theme-contexts` is defined)

**Explicit form** (still valid; same result):

```php
collection(path: '/', routes: __DIR__ . '/site.php', flows: ['theme.site']);

collection(path: '/panel', routes: __DIR__ . '/panel.php', flows: ['auth', 'theme.panel']);
```

You can mix styles: pass an explicit non-empty `path` to override the context path, and still use `context` for the theme flow.

---

## Guide: path, BASE, and loginUrl

These keys are **optional** per context. Use them when the panel (or another area) must not share the site’s auth redirects or client base URL.

| Key | Effect |
|-----|--------|
| `path` | Prefix for `collection(context: …)`. At render time, non-empty path joins onto `window.__PINOOX__.url.BASE` (path-only, e.g. `/myapp/panel`). |
| `auth.client` | Overlays app-level `auth.client` for `__PINOOX__.auth` (e.g. `loginUrl`). App `auth.client => false` still hides auth from the client. |

Example mental model:

```text
Request /panel/dashboard
  → collection context "panel"
  → flow theme.panel activates ThemeContext
  → Twig/Vite use admin theme
  → pinoox_bootstrap(): BASE ends with /panel, auth.loginUrl = /panel/auth/login
```

Site root context with `'path' => ''` leaves `BASE` as the normal app path and can still set `loginUrl` to `/login`.

---

## Context options

| Key | Required? | Description |
|-----|-----------|-------------|
| `theme` | Recommended | Theme folder under `theme/` |
| `extends` / `theme-extends` | No | Parent theme(s) for inheritance |
| `path-theme` | No | Theme root path (default `theme`) |
| `path` | No | URL prefix for collections + client `url.BASE` |
| `auth` | No | Per-context auth overlay (`client.loginUrl`, …) |
| `frontend` | No | Per-context Vite/stack settings |

---

## Runtime API

### Activate for current request

```php
theme_context('panel');
return View::render('dashboard.twig');
```

Portal:

```php
ThemeContext::activate('panel');
```

### Temporary switch

```php
$html = within_theme('panel', fn () => View::render('users.twig'));
```

### Inspect active context

```php
ThemeContext::active();   // e.g. panel
ThemeContext::info();     // context, theme name, stack paths
ThemeStack::resolve();    // respects active context
```

---

## Flow aliases helper

`theme_flow_aliases()` builds nested alias entries for `app.php`:

```php
theme_flow_aliases(['site', 'panel']);

// Route flow names: theme.site, theme.panel
```

Merge into `'alias'`. Without aliases, `flows: ['theme.panel']` / `collection(context: 'panel')` cannot resolve the flow class.

---

## Full example: site + panel

**`app.php` (excerpt)**

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site' => [
        'path' => '',
        'theme' => 'default',
        'auth' => ['client' => ['loginUrl' => '/login']],
    ],
    'panel' => [
        'path' => 'panel',
        'theme' => 'panel',
        'auth' => ['client' => ['loginUrl' => '/panel/auth/login']],
    ],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel']),
),
```

**`routes/web.php`**

```php
use function Pinoox\Router\collection;

collection(context: 'site', routes: __DIR__ . '/site/web.php');
collection(context: 'panel', routes: __DIR__ . '/panel/web.php', flows: ['auth']);
```

**`routes/panel/web.php`**

```php
use function Pinoox\Router\get;

get('/', [Panel\HomeController::class, 'index'])->name('panel.home');
get('/auth/login', [Panel\AuthController::class, 'login'])->name('panel.login');
```

Unauthenticated panel users are redirected using the panel `loginUrl`; the SPA reads the same value from `window.__PINOOX__.auth`.

---

## Controller without splitting collections

```php
public function previewKidsArea()
{
    return within_theme('kids', fn () => View::render('landing.twig'));
}
```

---

## Backward compatibility

- Omit or empty `theme-contexts` → single-theme behavior (`'theme' => '…'`).
- Explicit `path` + `flows: ['theme.panel']` keep working.
- Existing apps do **not** need to migrate to `collection(context: …)`.

---

## Site vs panel vs API

| Area | Typical context | Notes |
|------|-----------------|-------|
| Public website | `site` | SEO Twig shell, marketing Vite app |
| Admin / operator UI | `panel` | Separate theme + auth flow |
| Child / restricted UX | `kids` | Can extend `site` templates |
| JSON API routes | *(none)* | Do not attach `theme.*` flows |

Attach `theme.*` only on routes that render HTML.

---

## Frontend dev with contexts

When site and panel each have their own Vite theme (`package.json` + `frontend.config.php`):

```bash
php pinoox fe com_my_shop dev --theme=site
php pinoox fe com_my_shop dev --theme=panel
php pinoox fe com_my_shop dev --theme=all
php pinoox fe com_my_shop install --theme=all
php pinoox fe com_my_shop build --theme=panel
```

Per-context `frontend` in `app.php` merges into that theme’s `frontend.config.php`. See [Frontend & Vite](./frontend-vite.md).

---

## Related docs

- [Theme manifest (`theme.php`)](./theme-manifest.md) — inheritance inside a theme
- [Frontend & Vite](./frontend-vite.md) — profiles, CLI, Vite
- [Twig templates](./templates.md)
- [Router](./routers.md) — collections and flows
- [boot.php and events](../advanced/boot-and-events.md) — `onTheme` hooks
- [app.php manifest](../start/app-manifest.md)

---

[← Back to index](../README.md)
