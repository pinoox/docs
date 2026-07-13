# Theme contexts

[← Back to index](../README.md)

Use **theme contexts** when one app needs **multiple independent theme systems** — for example a public site, admin panel, kids area, or mobile shell — each with its own Twig/Vite theme folder and optional inheritance chain.

This is different from **theme inheritance** (`extends` in `theme.php` / `theme-extends`), which layers templates within a single active theme. Contexts switch **which theme tree** is active for the current request.

---

## Quick setup

### 1. Define contexts in `app.php`

```php
return [
    'package' => 'com_my_shop',

    // Default context when no flow is attached
    'theme-context' => 'site',

    'theme-contexts' => [
        'site' => [
            'theme' => 'default',
            'extends' => ['base'],
        ],
        'panel' => [
            'theme' => 'admin',
            'extends' => ['admin-base'],
            'frontend' => [
                'stack' => 'vue',
                'entry' => 'src/main.js',
            ],
        ],
        'kids' => [
            'theme' => 'kids',
            'extends' => ['site'],
        ],
    ],

    'alias' => array_merge([
        'auth' => AuthFlow::class,
    ], theme_flow_aliases(['site', 'panel', 'kids'])),
];
```

Folder layout:

```text
apps/com_my_shop/theme/
├── default/     # site
├── admin/       # panel
├── kids/
├── base/
└── admin-base/
```

### 2. Attach context to routes (Flow)

```php
// routes/web.php
use function Pinoox\Router\{collection, get};

collection(path: '/', routes: __DIR__ . '/site.php', flows: ['theme.site']);

collection(path: '/panel', routes: __DIR__ . '/panel.php', flows: ['auth', 'theme.panel']);

collection(path: '/kids', routes: __DIR__ . '/kids.php', flows: ['theme.kids']);
```

Each collection automatically renders with its own theme stack.

---

## Context options

| Key | Description |
|-----|-------------|
| `theme` | Theme folder name under `theme/` |
| `extends` / `theme-extends` | Parent theme(s) for inheritance |
| `path-theme` | Override theme root path (default `theme`) |
| `frontend` | Per-context Vite/stack settings |

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

`theme_flow_aliases()` registers ready-to-use flow objects:

```php
theme_flow_aliases(['site', 'panel', 'kids']);

// Equivalent route flow names:
// theme.site, theme.panel, theme.kids
```

Merge into `app.php` → `'alias'`.

---

## Controller without route collection split

```php
public function previewKidsArea()
{
    return within_theme('kids', fn () => View::render('landing.twig'));
}
```

---

## Backward compatibility

If `theme-contexts` is **empty or omitted**, Pinoox behaves as before:

```php
'theme' => 'default',
```

Single-theme apps need no changes.

---

## Site vs panel vs API

| Area | Typical context | Notes |
|------|-----------------|-------|
| Public website | `site` | SEO Twig shell, marketing Vite app |
| Admin / operator UI | `panel` | Separate admin theme + auth flow |
| Child / restricted UX | `kids` | Can extend `site` templates |
| JSON API routes | *(none)* | Do not attach `theme.*` flows |

Attach `theme.*` flows only on routes that render HTML views.

---

## Related docs

- [Theme manifest (`theme.php`)](./theme-manifest.md) — inheritance and metadata inside the theme
- [Frontend & Vite](./frontend-vite.md) — profiles, CLI, Vite wiring
- [Twig templates](./templates.md) — Twig helpers and layout
- [Router](./routers.md) — route collections and flows
- [boot.php and events](../advanced/boot-and-events.md) — `onTheme` hooks
- [app.php manifest](../start/app-manifest.md)

---

[← Back to index](../README.md)
