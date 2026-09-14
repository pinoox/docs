# app.php manifest referansı

[← Dizine dön](../README.md)

`app.php` uygulama manifest'inizdir. Varsayılanlar `vendor/pinoox/pincore/Component/Package/data/source.php` içinde yer alır — yalnızca ihtiyacınız olanları geçersiz kılın.

---

## Kimlik ve etkinleştirme

| Anahtar | Amaç |
|-----|---------|
| `package` | Klasör adı = namespace (`com_acme_shop`) |
| `name` | Görünen ad |
| `enable` | Uygulamayı etkinleştir / devre dışı bırak |
| `description`, `developer`, `icon` | Meta veri |
| `version-name`, `version-code` | Uygulama sürümü |
| `sys-app`, `hidden`, `dock` | Sistem uygulaması / gizli / manager dock |
| `minpin` | Minimum platform sürümü |

---

## Router ve boot

| Anahtar | Amaç |
|-----|---------|
| `router.routes` | `routes/*.php` dosyaları |
| `boot` | `boot.php` çalıştır (varsayılan true) |
| `boot-global` | Her HTTP isteğinde boot |
| `extends` | Host uygulama boot olduğunda boot |
| `loader` | Ek dosyalar (`func.php`) |
| `depends` | Gerekli uygulamalar |

Bkz. [boot.php ve event'ler](../advanced/boot-and-events.md).

---

## Flow ve güvenlik

| Anahtar | Amaç |
|-----|---------|
| `flow` | Global flow'lar (BootFlow) |
| `alias` | Ad → Flow sınıfı |
| `auth` | mod, süre, JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | Kullanıcı/dosya/erişimi platformla paylaş |

Bkz. [Flow'lar](../basic/flows.md), [Kullanıcı yönetimi](../advanced/user-management.md), [Erişim](../advanced/access-permissions.md).

---

## UI ve tema

| Anahtar | Amaç |
|-----|---------|
| `theme` | Aktif tema klasörü |
| `theme-context`, `theme-contexts`, `theme-extends` | Çoklu bağlam / kalıtım |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | Varsayılan yerel ayar |
| `open` | Manager açma davranışı |

---

## Veritabanı ve depolama

| Anahtar | Amaç |
|-----|---------|
| `database` | DB bağlantı geçersiz kılma |
| `table.prefix` | Tablo öneki |
| `transport.user` / `file_storage` / `access` | Ön ayarlar veya ayrıntılı anahtarlar |
| `filesystem` | disk, hash_length, dispatcher, file_policy, groups, thumbs |

---

## Runtime

| Anahtar | Amaç |
|-----|---------|
| `runtime.mode`, `runtime.debug` | Mod geçersiz kılmaları |
| `cache` | Route/api/boot/twig bake |
| `log`, `redis`, `date` | Uygulama başına geçersiz kılmalar |
| `container` | DI bağlamaları |

---

## Pinker / Pinx

| Anahtar | Amaç |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | paketler için exclude/include |

---

## Birleşik örnek

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

## İlgili dokümantasyon

- [Proje yapısı](./structure.md)
- [Config](../basic/config.md)

---

[← Dizine dön](../README.md)
