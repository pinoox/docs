# Project structure

[← Back to index](../../readme.md)

Pinoox uses HMVC architecture: each app under `apps/{package}/` is a complete, independent MVC module. The framework core lives in `vendor/pinoox/pincore/` and is edited only when changing the platform itself.

---

## Project layout

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← core (Composer package)
├── apps/                    ← all apps
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── uploads/
```

---

## App layout

```
apps/com_acme_shop/
├── app.php                  ← manifest (required)
├── boot.php                 ← programmatic routes/events (optional)
├── schedule.php             ← cron tasks (optional)
├── Controller/              ← HTTP handlers
├── Model/                   ← Eloquent models
├── Flow/                    ← middleware
├── Component/               ← business logic
├── Portal/                  ← app facades (optional)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← action name constants (optional)
├── theme/default/           ← Twig + assets
├── lang/en/                 ← translations
├── config/                  ← app config
├── database/migrations/
└── pinker/                  ← build mirror
```

Views are not in a separate `View/` folder — templates live in `theme/{themeName}/`.

---

## app.php — key fields

```php
<?php

return [
    'package' => 'com_acme_shop',   // = folder name
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Namespaces

PSR-4: `App\` → `apps/`

| File | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## Naming rules

- Package: `com_{vendor}_{name}` — e.g. `com_acme_shop`
- Folder name = `package` in `app.php` = namespace segment
- DB table prefix: `{package}_` (e.g. `com_acme_shop_orders`)

---

## App vs core boundary

| Change | Location |
|--------|----------|
| New endpoint | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| Framework bug | `pinoox/pincore` (upstream) |
| UI | `apps/{package}/theme/` |

Keep apps independent — use `Pinoox\Portal\*` facades rather than coupling apps to each other.

---

## Related docs

- [Your first app](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← Back to index](../../readme.md)
