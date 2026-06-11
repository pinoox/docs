# Proje yapısı

[← Dizine dön](../README.md)

Pinoox HMVC mimarisi kullanır: `apps/{package}/` altındaki her uygulama tam, bağımsız bir MVC modülüdür. Framework çekirdeği `vendor/pinoox/pincore/` içinde yer alır ve yalnızca platformun kendisini değiştirirken düzenlenir.

---

## Proje düzeni

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← çekirdek (Composer paketi)
├── apps/                    ← tüm uygulamalar
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← yüklenen dosyalar ve uygulama depolama
```

---

## Uygulama düzeni

```
apps/com_acme_shop/
├── app.php                  ← manifest (zorunlu)
├── boot.php                 ← programatik route'lar/event'ler (isteğe bağlı)
├── schedule.php             ← cron görevleri (isteğe bağlı)
├── Controller/              ← HTTP handler'ları
├── Model/                   ← Eloquent model'leri
├── Flow/                    ← middleware
├── Component/               ← iş mantığı
├── Portal/                  ← uygulama facade'leri (isteğe bağlı)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← action adı sabitleri (isteğe bağlı)
├── theme/default/           ← Twig + asset'ler
├── lang/en/                 ← çeviriler
├── config/                  ← uygulama config'i
├── database/migrations/
└── pinker/                  ← derleme aynası
```

View'lar ayrı bir `View/` klasöründe değildir — şablonlar `theme/{themeName}/` içinde yer alır.

---

## app.php — temel alanlar

```php
<?php

return [
    'package' => 'com_acme_shop',   // = klasör adı
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

## Namespace'ler

PSR-4: `App\` → `apps/`

| Dosya | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## Adlandırma kuralları

- Paket: `com_{vendor}_{name}` — ör. `com_acme_shop`
- Klasör adı = `app.php` içindeki `package` = namespace segmenti
- DB tablo öneki: `{package}_` (ör. `com_acme_shop_orders`)

---

## Uygulama ve çekirdek sınırı

| Değişiklik | Konum |
|--------|----------|
| Yeni endpoint | `apps/{package}/Controller/` + `routes/` |
| Migration | `apps/{package}/database/migrations/` |
| Framework hatası | `pinoox/pincore` (upstream) |
| UI | `apps/{package}/theme/` |

Uygulamaları bağımsız tutun — uygulamaları birbirine bağlamak yerine `Pinoox\Portal\*` facade'lerini kullanın.

---

## İlgili dokümantasyon

- [İlk uygulamanız](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controller](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← Dizine dön](../README.md)
