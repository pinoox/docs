# Single-App Project Structure

[Back to index](../README.md)

In the recommended Pinx workflow, the project root is the app root.

There is no `apps/{package}` folder in day-to-day development. Your controllers, models, routes, theme, migrations, and config live directly in the project.

---

## Pinx Project Layout

```text
my-shop/
├── app.php
├── .env
├── composer.json
├── index.php
├── bin/pinx
├── Controller/
├── Model/
├── routes/
│   ├── web.php
│   └── actions.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── theme/
│   └── default/
├── lang/
├── config/
├── schedule.php
├── platform/
├── storage/
├── pinker/
├── export/
└── vendor/
```

| Path | Purpose |
| --- | --- |
| `app.php` | app identity, version, theme, build, signing, frontend settings |
| `.env` | local environment; minimal by default |
| `Controller/` | HTTP controllers |
| `Model/` | Eloquent-style models |
| `routes/` | web/API/action routes |
| `database/migrations/` | schema changes |
| `database/seeders/` | local/demo/initial data |
| `database/factories/` | test and development records |
| `theme/default/` | Twig templates and assets |
| `lang/` | app translations |
| `config/` | app configuration |
| `schedule.php` | scheduled jobs |
| `platform/` | local platform launcher and routing; excluded from `.pinx` |
| `storage/` | logs, sessions, DevDB data, uploaded files |
| `pinker/` | build/cache metadata |
| `export/` | generated `.pinx` packages |

---

## Namespace Rules

Pinx maps your project app code to the `App\` namespace.

| File | Namespace |
| --- | --- |
| `Controller/PostController.php` | `App\Controller` |
| `Model/Post.php` | `App\Model` |
| `Flow/AuthFlow.php` | `App\Flow` |
| `database/factories/PostFactory.php` | `App\database\factories` |

---

## The app.php Manifest

Example:

```php
<?php

return [
    'package' => 'com_acme_shop',
    'name' => 'Acme Shop',
    'description' => 'Shop app built with Pinoox',
    'developer' => 'Acme',
    'icon' => 'resource/icon.png',
    'version-name' => '1.0.0',
    'version-code' => 1,
    'enable' => true,
    'theme' => 'default',
    'lang' => 'en',
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
    'pinx' => [
        'sign' => [
            'enabled' => false,
            'key' => null,
            'key_id' => null,
        ],
    ],
];
```

See [app.php manifest reference](./app-manifest.md).

---

## What Is Excluded From Builds

`pinx build` creates a `.pinx` app package. It excludes local-only files by default:

- `vendor/`
- `bin/`
- `.env`
- `platform/`
- `storage/`
- `export/`
- development tooling

Configure exceptions in `app.php` only when needed.

---

## Classic Platform Layout

The full platform still supports multiple apps under `apps/{package}`. That layout is useful for platform maintainers and multi-app hosting. For new app development, prefer Pinx single-app projects.

---

## Next

- [Create your first app](./your-first-app.md)
- [Pinx CLI](./pinx-cli.md)
- [Build and release](./build-release.md)
