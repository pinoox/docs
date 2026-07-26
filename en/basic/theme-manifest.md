# Theme manifest (`theme.php`)

[← Back to index](../README.md)

Each theme folder carries its own manifest. **Metadata and inheritance live inside the theme** — not in `app.php`.

```text
apps/{package}/theme/{theme-name}/
├── theme.php          ← manifest (required for pinx theme packages & manager listing)
├── main.twig
├── cover.png
└── dist/
```

---

## Standard `theme.php`

```php
<?php

return [
    'name' => 'toranj',
    'package' => 'com_pinoox_paper',
    'extends' => ['blue'],
    'cover' => 'cover.png',
    'developer' => 'pinoox',
    'copyright' => 'MIT',
    'version-name' => '1.0',
    'version-code' => 1,
    'api' => true,
    'title' => [
        'en' => 'Toranj',
        'fa' => 'ترنج',
    ],
    'description' => [
        'en' => 'Minimal blog template',
        'fa' => 'قالب مینیمال وبلاگ',
    ],
];
```

### Field reference

| Field | Required | Description |
|-------|----------|-------------|
| `name` | yes | Theme folder slug (usually matches directory name) |
| `package` | yes* | Host app package (`com_pinoox_paper`) |
| `app` | legacy | Alias of `package` (old `.pin` themes) |
| `extends` | no | Parent theme(s) — string or array |
| `cover` | no | Preview image relative to theme folder |
| `developer` | no | Author / team |
| `copyright` | no | License note |
| `version-name` / `version` | no | Theme version label |
| `version-code` / `app_version` | no | Numeric theme version |
| `api` | no | Theme exposes API-oriented shell |
| `title` | no | Localized display name (`en`, `fa`, …) |
| `description` | no | Localized description |

\* Required for distribution (`pinx` theme build) and manager listing.

---

## Inheritance (`extends`)

Define parents **inside the child theme**:

```php
return [
    'name' => 'toranj',
    'package' => 'com_pinoox_paper',
    'extends' => ['blue'],
];
```

Cross-app parent:

```php
'extends' => ['default', '@com_pinoox_welcome/welcome'],
```

Resolution order:

1. **`theme.php` → `extends`** (primary)
2. **`app.php` → `theme-extends`** (deprecated fallback when theme manifest has no extends)

`app.php` should only set the **active theme name** (or use [theme contexts](./theme-contexts.md)). Do not duplicate inheritance there.

---

## PHP API

```php
use Pinoox\Component\Template\Theme\ThemeManifest;
use Pinoox\Component\Template\Theme\ThemeStack;

$manifest = ThemeManifest::load('com_pinoox_paper', 'toranj');
$manifest->title('fa');
$manifest->extends();

$themes = ThemeManifest::discover('com_pinoox_paper');

$stack = ThemeStack::resolve('com_pinoox_paper'); // uses active theme + manifest extends
```

---

## Pinx theme packages

Build a distributable theme:

```php
// app.php
'pinx' => [
    'type' => 'theme',
    'target_app' => 'com_pinoox_paper',
    'theme_name' => 'toranj',
],
```

```bash
php pinoox pinx:build com_pinoox_paper
```

Requirements:

- `theme/toranj/theme.php` must exist
- `theme.php` `package` must match host app
- `manifest.json` inside `.pinx` embeds `theme_meta` and uses theme name as package id

Install validates `theme.php` after extraction.

Legacy `.pin` themes with root `theme.php` remain supported via `PinxReader`.

---

## Scaffold

Copy stub when creating a theme:

```text
pincore/stubs/theme.php.stub
```

`php pinoox app:create` writes `theme/default/theme.php` from this stub.

---

## Manager integration

`com_pinoox_manager` lists themes via `ThemeManifest::discover()` — only folders with `theme.php` appear in the template picker.

---

## Related docs

- [Theme contexts](./theme-contexts.md)
- [Twig templates](./templates.md)
- [Frontend & Vite](./frontend-vite.md)
- [Pinx CLI](../start/pinx-cli.md)
- [app.php manifest](../start/app-manifest.md)

---

[← Back to index](../README.md)
