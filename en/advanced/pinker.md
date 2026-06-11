# Pinker and Cache

[← Back to index](../README.md)

**Pinker** is the bake/runtime layer in Pinoox 3.x: config and cache are compiled from source into PHP files that can be `include`d for faster boot. Standard path per app: **`pinker/apps/{package}/`**.

---

## Folder structure

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

At project level:

```
pinker/config/          ← baked config (non env-sensitive)
pinker/state/config/    ← post-install overrides (e.g. database)
```

---

## CLI commands

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

## When to rebuild

| Event | Command |
|-------|---------|
| Change `app.php` or config | `pinker:rebuild` |
| Change route / api | `cache:build` |
| Change `.twig` in production | `cache:build --only=twig` |
| After server install | `cache:build` + `pinker:rebuild` |
| Before building `.pinx` | `cache:build` (cache inside package) |

---

## Enable cache at runtime

In `apps/{package}/app.php`:

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

Each app can have a baked mirror:

```
apps/com_acme_shop/pinker/app.php   ← source/reference in repo
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## `pinker()` helper

For manual data baking:

```php
pinker($data, ['lifetime' => 3600]);
```

Usually you use CLI instead; rarely needed in app code.

---

## Recommended deploy workflow

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

- Do not edit `pinker/state/` manually — the installer writes there.
- In development runtime cache is usually off; rebuild only after heavy changes.
- `.pinx` can ship pre-built cache; on the target server run `cache:build --only=pinker` once.

---

## Related docs

- [Config](../basic/config.md)
- [Twig Templates](../basic/templates.md)
- [CLI Reference](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← Back to index](../README.md)
