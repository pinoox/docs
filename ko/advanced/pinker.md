# Pinker and Cache

[← 색인으로 돌아가기](../README.md)

**Pinker**는 Pinoox 3.x의 bake/runtime 계층입니다: config와 cache가 source에서 `include` 가능한 PHP file로 compile되어 더 빠른 boot를 제공합니다. 앱당 표준 경로: **`pinker/apps/{package}/`**.

---

## 폴더 구조

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← baked app.php
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← compiled templates
```

프로젝트 수준:

```
pinker/config/          ← baked config (non env-sensitive)
pinker/state/config/    ← post-install overrides (e.g. database)
```

---

## CLI command

```bash
# Rebuild Pinker for one app
php pinoox pinker:rebuild com_acme_shop

# Short alias
php pinoox bake com_acme_shop

# Status: compare source vs baked output
php pinoox pinker:status com_acme_shop

# Build cache (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# Twig only
php pinoox cache:build com_acme_shop --only=twig

# Pinker only
php pinoox cache:build com_acme_shop --only=pinker

# Clear cache
php pinoox cache:clear com_acme_shop
```

---

## Rebuild 시점

| Event | Command |
|-------|---------|
| Change `app.php` or config | `pinker:rebuild` |
| Change route / api | `cache:build` |
| Change `.twig` in production | `cache:build --only=twig` |
| After server install | `cache:build` + `pinker:rebuild` |
| Before building `.pinx` | `cache:build` (cache inside package) |

---

## Runtime에서 cache 활성화

`apps/{package}/app.php`에서:

```php
'cache' => [
    'enabled' => false,   // default — set true in production if needed
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## App mirror — `pinker/app.php`

각 앱은 baked mirror를 가질 수 있습니다:

```
apps/com_acme_shop/pinker/app.php   ← source/reference in repo
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## `pinker()` helper

수동 data baking:

```php
pinker($data, ['lifetime' => 3600]);
```

보통 CLI를 사용; 앱 code에서 필요한 경우는 드뭅니다.

---

## 권장 deploy workflow

```bash
# 1. build frontend
php pinoox theme:frontend build com_acme_shop

# 2. cache
php pinoox cache:build com_acme_shop

# 3. pinker (env-specific)
php pinoox pinker:rebuild com_acme_shop
```

---

## Tips

- `pinker/state/`를 수동 편집하지 마세요 — installer가 작성합니다.
- development runtime cache는 보통 off; 큰 변경 후에만 rebuild.
- `.pinx`는 pre-built cache를 포함할 수 있음; target server에서 `cache:build --only=pinker` 한 번 실행.

---

## 관련 문서

- [Config](../basic/config.md)
- [Twig Templates](../basic/templates.md)
- [CLI Reference](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← 색인으로 돌아가기](../README.md)
