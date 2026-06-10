# app.php manifest reference

[← Back to index](../../README.md)

`app.php` is your app manifest. Defaults live in `vendor/pinoox/pincore/Component/Package/data/source.php` — override only what you need.

---

## Identity & activation

| Key | Purpose |
|-----|---------|
| `package` | Folder name = namespace (`com_acme_shop`) |
| `name` | Display name |
| `enable` | Enable / disable app |
| `description`, `developer`, `icon` | Metadata |
| `version-name`, `version-code` | App version |
| `sys-app`, `hidden`, `dock` | System app / hidden / manager dock |
| `minpin` | Minimum platform version |

---

## Router & boot

| Key | Purpose |
|-----|---------|
| `router.routes` | `routes/*.php` files |
| `boot` | Run `boot.php` (default true) |
| `boot-global` | Boot on every HTTP request |
| `extends` | Boot when host app boots |
| `loader` | Extra files (`func.php`) |
| `depends` | Required apps |

See [boot.php & events](../advanced/boot-and-events.md).

---

## Flow & security

| Key | Purpose |
|-----|---------|
| `flow` | Global flows (BootFlow) |
| `alias` | Name → Flow class |
| `auth` | mode, lifetime, JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | Share user/file/access with platform |

See [Flows](../basic/flows.md), [User management](../advanced/user-management.md), [Access](../advanced/access-permissions.md).

---

## UI & theme

| Key | Purpose |
|-----|---------|
| `theme` | Active theme folder |
| `theme-context`, `theme-contexts`, `theme-extends` | Multi-context / inheritance |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | Default locale |
| `open` | Manager open behavior |

---

## Database & storage

| Key | Purpose |
|-----|---------|
| `database` | DB connection override |
| `table.prefix` | Table prefix |
| `transport.user` / `file_storage` / `access` | Presets or granular keys |
| `filesystem` | disk, thumbs, access |

---

## Runtime

| Key | Purpose |
|-----|---------|
| `runtime.mode`, `runtime.debug` | Mode overrides |
| `cache` | Bake routes/api/boot/twig |
| `log`, `redis`, `date` | Per-app overrides |
| `container` | DI bindings |

---

## Pinker / Pinx

| Key | Purpose |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | exclude/include for packages |

---

## Combined example

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## Related docs

- [Project structure](./structure.md)
- [Config](../basic/config.md)

---

[← Back to index](../../README.md)
