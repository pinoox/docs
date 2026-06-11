# 프로젝트 구조

[← 색인으로 돌아가기](../README.md)

Pinoox는 HMVC 아키텍처를 사용합니다. `apps/{package}/` 아래 각 앱은 완전하고 독립적인 MVC 모듈입니다. 프레임워크 코어는 `vendor/pinoox/pincore/`에 있으며 플랫폼 자체를 변경할 때만 편집합니다.

---

## 프로젝트 레이아웃

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
└── storage/                 ← uploaded files & app storage
```

---

## 앱 레이아웃

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

View는 별도 `View/` 폴더에 있지 않습니다 — 템플릿은 `theme/{themeName}/`에 있습니다.

---

## app.php — 주요 필드

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

## Namespace

PSR-4: `App\` → `apps/`

| File | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## 명명 규칙

- Package: `com_{vendor}_{name}` — 예: `com_acme_shop`
- 폴더 이름 = `app.php`의 `package` = namespace segment
- DB 테이블 접두사: `{package}_` (예: `com_acme_shop_orders`)

---

## 앱 vs 코어 경계

| 변경 | 위치 |
|--------|----------|
| New endpoint | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| Framework bug | `pinoox/pincore` (upstream) |
| UI | `apps/{package}/theme/` |

앱을 독립적으로 유지하세요 — 앱끼리 결합하지 말고 `Pinoox\Portal\*` facade를 사용하세요.

---

## 관련 문서

- [첫 번째 앱 만들기](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← 색인으로 돌아가기](../README.md)
