# boot.php ve event'ler

[← Dizine dön](../README.md)

`routes/` dışında route'ları, API endpoint'lerini, flow'ları, zamanlamaları ve listener'ları **`boot.php`** içinde kaydedebilirsiniz — **eklentiler**, mikro modüller veya host uygulamaya kancalar (ör. manager) için kullanışlıdır.

Her app `apps/{package}/boot.php` sağlayabilir. Dosya `AppRegister` alan bir closure döndürür ve istek işlenmeden **önce** çalışır.

---

## Yaşam döngüsü

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### Pipeline aşamaları

| Aşama | Amaç |
|-------|------|
| `boot.global` | Her istekte `boot-global => true` app'leri boot et |
| `app.boot` | Aktif route app boot (+ `extends` ile extender'lar) |

### Boot event'leri

| Ad | Ne zaman |
|----|----------|
| `app.booting` / `app.booting.{package}` | commit öncesi |
| `app.booted` / `app.booted.{package}` | integrate sonrası |
| `app.routes` / `app.routes.{package}` | web route uygulanırken |
| `app.api` / `app.api.{package}` | API registry oluşturulurken |

`boot.php` içinden dinleme:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### Çekirdek request event'leri

Her HTTP isteğinde framework tarafından otomatik (`AppCoreEventSubscriber`):

| Ad | Ne zaman | package varyantı | Adlandırılmış kanal |
|----|----------|------------------|---------------------|
| `app.route.matched` | route eşleşmesinden sonra | `app.route.matched.{package}` | `app.route.{routeName}` veya `app.api.{routeName}` |
| `app.controller` | controller öncesi | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | yanıt gönderilmeden önce | `app.response.{package}` | — |
| `app.exception` | yakalanmamış exception | `app.exception.{package}` | — |
| `app.terminate` | yanıt gönderildikten sonra | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

Basit hook'lar için **watch** (`onRoute`, `onApi`, …); tam kontrol için çekirdek event'lere **listen**.

---

## Üç uygulama modu

| Mod | Config | Davranış |
|------|--------|----------|
| **Yalnızca route** | yalnızca `router.routes` | Uygulama URL'si aktif olduğunda çalışır |
| **Boot global** | `boot-global => true` | Her HTTP isteğinde boot |
| **Boot + Route** | `boot.php` + route'lar | Varsayılan iskelet |

Host uygulamadaki eklenti:

```php
'extends' => ['com_host_app'],
```

Eklentiniz yalnızca host boot olduğunda boot olur (global'den daha hafif).

---

## boot için `app.php` anahtarları

`apps/{package}/app.php` içindeki bu anahtarlar `boot.php`'nin **ne zaman** çalışacağını ve önbelleğe alınıp alınmayacağını belirler. Boot pipeline'ını yapılandırır — `boot.php`'nin yerini almaz.

### Boot dosyası (`boot`)

| Değer | Varsayılan | Etki |
|-------|------------|------|
| `true` | evet | App boot olurken `boot.php` çalıştır |
| `false` | | Boot yok — yalnızca route |
| `'path/custom.php'` | | App köküne göre başka dosya |

Dosya **callable döndürmeli**: `fn (AppRegister $register) => …`.

### Global plugin (`boot-global`)

| Değer | Varsayılan | Etki |
|-------|------------|------|
| `false` | evet | Yalnızca bu app aktifken boot |
| `true` | | **Her HTTP isteğinde** boot |

### Host plugin (`extends`)

| Değer | Varsayılan | Etki |
|-------|------------|------|
| `[]` | evet | Normal app |
| `['com_host_app']` | | Host aktif olunca **önce** boot |

### Ek kayıt (`startup`)

`app.php` içinde optional callable, `boot.php`'den **sonra**.

### Boot önbelleği (`cache`)

Opt-in: `cache.enabled` = `true`. Deploy sonrası: `php pinoox cache:build {package}`.

### Hızlı seçim

| Amaç | Ayar |
|------|------|
| Normal app | `'boot' => true` |
| Yalnızca route | `'boot' => false` |
| Site geneli plugin | `'boot-global' => true` |
| Host plugin | `'extends' => ['com_host_app']` |

---

## Temel boot.php

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
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

## Theme — context, kalıtım ve boot kancaları

Klasörler `apps/{package}/theme/{name}/` altında. Aktif theme **`app.php`**; runtime kancalar **`boot.php`**.

### `app.php` anahtarları

| Anahtar | Amaç |
|---------|------|
| `theme` | Aktif theme klasörü |
| `theme-context` / `theme-contexts` | Birden fazla theme |
| `theme-extends` | Kalıtım |
| `path-theme` | Özel yol |
| `frontend` | Vite profile, entry, manifest |

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

Routes: `flows: ['auth', 'theme.panel']`. `theme/{name}/`: `theme.php`, Twig, `functions.php`, `frontend.config.php`, `src/` / `dist/`.

Bkz. [Views](../basic/views.md), [Twig](../basic/templates.md), [app.php](../start/app-manifest.md).

### `boot.php` içinden

**`onTheme`** veya **listen** / **watch**:

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});
```

Controller: `View::changeTheme('panel')`, `ThemeContext::activate('panel')`, `within_theme(...)`.

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

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
