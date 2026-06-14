# boot.php और Events

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

`routes/` के अलावा, आप routes, API endpoints, flows, schedules और listeners को **`boot.php`** में भी रजिस्टर कर सकते हैं — यह **plugins**, micro-modules, या किसी host ऐप (जैसे manager) में hooks के लिए उपयोगी है।

प्रत्येक app `apps/{package}/boot.php` रख सकता है। फ़ाइल एक closure लौटाती है जो `AppRegister` लेता है और request handle होने से **पहले** चलता है।

---

## जीवनचक्र (Lifecycle)

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### Pipeline चरण

| चरण | उद्देश्य |
|-----|----------|
| `boot.global` | हर request पर `boot-global => true` वाले apps boot |
| `app.boot` | सक्रिय route app boot (+ `extends` से extenders) |

### Boot events

| नाम | कब |
|-----|-----|
| `app.booting` / `app.booting.{package}` | commit से पहले |
| `app.booted` / `app.booted.{package}` | integrate के बाद |
| `app.routes` / `app.routes.{package}` | web routes लागू होने पर |
| `app.api` / `app.api.{package}` | API registry बनते समय |

`boot.php` से सुनें:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### Core request events

हर HTTP request पर framework द्वारा स्वचालित (`AppCoreEventSubscriber`):

| नाम | कब | package variant | नामित channel |
|-----|-----|-----------------|---------------|
| `app.route.matched` | route match के बाद | `app.route.matched.{package}` | `app.route.{routeName}` या `app.api.{routeName}` |
| `app.controller` | controller से पहले | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | response भेजने से पहले | `app.response.{package}` | — |
| `app.exception` | uncaught exception पर | `app.exception.{package}` | — |
| `app.terminate` | response भेजने के बाद | `app.terminate.{package}` | — |

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

सरल hooks के लिए **watches** (`onRoute`, `onApi`, …); पूरा नियंत्रण के लिए core events पर **listen**।

---

## तीन ऐप मोड

| मोड | कॉन्फ़िग | व्यवहार |
|------|--------|----------|
| **केवल Route** | केवल `router.routes` | जब ऐप का URL सक्रिय हो तब चलता है |
| **Boot global** | `boot-global => true` | हर HTTP request पर boot होता है |
| **Boot + Route** | `boot.php` + routes | डिफ़ॉल्ट scaffold |

किसी host ऐप पर plugin:

```php
'extends' => ['com_host_app'],
```

आपका plugin केवल तभी boot होता है जब host boot होता है (global से हल्का)।

---

## boot के लिए `app.php` कुंजियाँ

`apps/{package}/app.php` में ये कुंजियाँ नियंत्रित करती हैं कि `boot.php` **चले या नहीं**, **कब** चले, और cache हो या नहीं। ये boot pipeline configure करती हैं — `boot.php` की जगह नहीं लेतीं।

### boot फ़ाइल (`boot`)

| मान | डिफ़ॉल्ट | प्रभाव |
|-----|----------|---------|
| `true` | हाँ | app boot पर `boot.php` चलाएँ |
| `false` | | boot नहीं — केवल routes |
| `'path/custom.php'` | | app root से दूसरी फ़ाइल |

फ़ाइल को **callable** लौटाना चाहिए: `fn (AppRegister $register) => …`.

### global plugin (`boot-global`)

| मान | डिफ़ॉल्ट | प्रभाव |
|-----|----------|---------|
| `false` | हाँ | केवल जब यह app सक्रिय हो |
| `true` | | **हर HTTP request** पर boot |

### host plugin (`extends`)

| मान | डिफ़ॉल्ट | प्रभाव |
|-----|----------|---------|
| `[]` | हाँ | सामान्य app |
| `['com_host_app']` | | host सक्रिय होने पर **पहले** boot |

### अतिरिक्त registration (`startup`)

`app.php` में optional callable, `boot.php` के **बाद**।

### boot cache (`cache`)

Opt-in: `cache.enabled` = `true`. deploy के बाद: `php pinoox cache:build {package}`.

### त्वरित चयन

| लक्ष्य | सेटिंग |
|--------|---------|
| सामान्य app | `'boot' => true` |
| केवल routes | `'boot' => false` |
| site-wide plugin | `'boot-global' => true` |
| host plugin | `'extends' => ['com_host_app']` |

---

## बुनियादी boot.php

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

## AppRegister — सामान्य मेथड्स

| मेथड | उद्देश्य |
|--------|---------|
| `web(callable)` | Builder के माध्यम से routes रजिस्टर करें |
| `route([...])` | एक single web route |
| `api([manifest])` | पूर्ण API manifest |
| `apiRoute([...])` | एक single API endpoint |
| `action('name', handler)` | नामित Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow alias |
| `schedule(callable)` | शेड्यूल्ड task |
| `listen('event', listener)` | Event listener |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | जब कोई दूसरा ऐप boot हो तब hook करें |

---

## Event portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Controllers से mail को अलग (decouple) करने के लिए [Email](./mail.md) देखें।

**Flow** = controller से पहले (middleware)। **Event** = किसी action के बाद (side effects)।

---

## Helpers

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot कैश

`app.php` में `cache.stores` के अंतर्गत `'boot' => true` boot को Pinker के माध्यम से bake करता है — देखें [Pinker](./pinker.md)।

---

## संबंधित दस्तावेज़

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Routers](../basic/routers.md)
- [Project structure](../start/structure.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)
