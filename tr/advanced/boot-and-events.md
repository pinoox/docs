# boot.php ve event'ler

[← Dizine dön](../README.md)

`routes/` dışında route'ları, API endpoint'lerini, flow'ları, zamanlamaları ve listener'ları **`boot.php`** içinde kaydedebilirsiniz — **eklentiler**, mikro modüller veya host uygulamaya kancalar (ör. manager) için kullanışlıdır.

---

## Üç uygulama modu

| Mod | Config | Davranış |
|------|--------|----------|
| **Yalnızca route** | yalnızca `router.routes` | Uygulama URL'si aktif olduğunda çalışır |
| **Boot global** | `boot-global => true` | Her HTTP isteğinde boot |
| **Boot + Route** | `boot.php` + route'lar | Varsayılan iskelet |

Host uygulamadaki eklenti:

```php
'extends' => ['com_pinoox_manager'],
```

Eklentiniz yalnızca host boot olduğunda boot olur (global'den daha hafif).

---

## Temel boot.php

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => response()->json(['ok' => true]),
        'name' => 'health',
    ]);

    $register->when('com_pinoox_manager', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => response()->json(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['manager.auth'],
        ]);
    });
};
```

---

## AppRegister — yaygın metotlar

| Metot | Amaç |
|--------|---------|
| `web(callable)` | Builder üzerinden route kaydet |
| `route([...])` | Tek web route'u |
| `api([manifest])` | Tam API manifest'i |
| `apiRoute([...])` | Tek API endpoint'i |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow takma adı |
| `schedule(callable)` | Zamanlanmış görev |
| `listen('event', listener)` | Event listener |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | Başka uygulama boot olduğunda kanca |

---

## Event portal'ı

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

E-postayı controller'lardan ayırmak için bkz. [E-posta](./mail.md).

**Flow** = controller'dan önce (middleware). **Event** = bir action'dan sonra (yan etkiler).

---

## Helper'lar

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot önbelleği

`app.php` içinde `cache.stores` altında `'boot' => true`, boot'u Pinker üzerinden bake eder — bkz. [Pinker](./pinker.md).

---

## İlgili dokümantasyon

- [Zamanlama](./schedule.md)
- [Flow'lar](../basic/flows.md)
- [Router](../basic/routers.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)
