# app.php 매니페스트 참조

[← 색인으로 돌아가기](../README.md)

`app.php`는 앱 매니페스트입니다. 기본값은 `vendor/pinoox/pincore/Component/Package/data/source.php`에 있으며 필요한 것만 override하세요.

---

## Identity & activation

| Key | Purpose |
|-----|---------|
| `package` | 폴더 이름 = namespace (`com_acme_shop`) |
| `name` | Display name |
| `enable` | 앱 활성화 / 비활성화 |
| `description`, `developer`, `icon` | Metadata |
| `version-name`, `version-code` | App version |
| `sys-app`, `hidden`, `dock` | System app / hidden / manager dock |
| `minpin` | Minimum platform version |

---

## Router & boot

| Key | Purpose |
|-----|---------|
| `router.routes` | `routes/*.php` files |
| `boot` | `boot.php` 실행 (default true) |
| `boot-global` | 모든 HTTP request에서 boot |
| `extends` | Host app boot 시 boot |
| `loader` | Extra files (`func.php`) |
| `depends` | Required apps |

[boot.php & events](../advanced/boot-and-events.md) 참조.

---

## Flow & security

| Key | Purpose |
|-----|---------|
| `flow` | Global flows (BootFlow) |
| `alias` | Name → Flow class |
| `auth` | mode, lifetime, JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | Share user/file/access with platform |

[Flows](../basic/flows.md), [User management](../advanced/user-management.md), [Access](../advanced/access-permissions.md) 참조.

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

## 관련 문서

- [프로젝트 구조](./structure.md)
- [Config](../basic/config.md)

---

[← 색인으로 돌아가기](../README.md)
