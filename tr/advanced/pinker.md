# Pinker ve önbellek

[← Dizine dön](../README.md)

**Pinker**, Pinoox 3.x'te bake/runtime katmanıdır: config ve önbellek kaynaktan derlenir ve daha hızlı boot için `include` edilebilen PHP dosyalarına dönüştürülür. Uygulama başına standart yol: **`pinker/apps/{package}/`**.

---

## Klasör yapısı

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

Proje düzeyinde:

```
pinker/config/          ← baked config (non env-sensitive)
pinker/state/config/    ← post-install overrides (e.g. database)
```

---

## CLI komutları

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

## Ne zaman yeniden oluşturulmalı

| Olay | Komut |
|-------|---------|
| `app.php` veya config değişikliği | `pinker:rebuild` |
| route / api değişikliği | `cache:build` |
| Üretimde `.twig` değişikliği | `cache:build --only=twig` |
| Sunucu kurulumundan sonra | `cache:build` + `pinker:rebuild` |
| `.pinx` oluşturmadan önce | `cache:build` (paket içinde önbellek) |

---

## Çalışma zamanında önbelleği etkinleştirme

`apps/{package}/app.php` içinde:

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

## Uygulama aynası — `pinker/app.php`

Her uygulamanın bake edilmiş bir aynası olabilir:

```
apps/com_acme_shop/pinker/app.php   ← source/reference in repo
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## `pinker()` helper'ı

Manuel veri bake için:

```php
pinker($data, ['lifetime' => 3600]);
```

Genellikle CLI kullanırsınız; uygulama kodunda nadiren gerekir.

---

## Önerilen dağıtım iş akışı

```bash
# 1. build frontend
php pinoox theme:frontend build com_acme_shop

# 2. cache
php pinoox cache:build com_acme_shop

# 3. pinker (env-specific)
php pinoox pinker:rebuild com_acme_shop
```

---

## İpuçları

- `pinker/state/` dosyalarını manuel düzenlemeyin — yükleyici oraya yazar.
- Geliştirmede runtime önbelleği genellikle kapalıdır; yalnızca büyük değişikliklerden sonra yeniden oluşturun.
- `.pinx` önceden derlenmiş önbellek taşıyabilir; hedef sunucuda bir kez `cache:build --only=pinker` çalıştırın.

---

## İlgili dokümantasyon

- [Config](../basic/config.md)
- [Twig şablonları](../basic/templates.md)
- [CLI referansı](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← Dizine dön](../README.md)
