# boot.php और Events

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

`routes/` के अलावा, आप routes, API endpoints, flows, schedules और listeners को **`boot.php`** में भी रजिस्टर कर सकते हैं — यह **plugins**, micro-modules, या किसी host ऐप (जैसे manager) में hooks के लिए उपयोगी है।

---

## तीन ऐप मोड

| मोड | कॉन्फ़िग | व्यवहार |
|------|--------|----------|
| **केवल Route** | केवल `router.routes` | जब ऐप का URL सक्रिय हो तब चलता है |
| **Boot global** | `boot-global => true` | हर HTTP request पर boot होता है |
| **Boot + Route** | `boot.php` + routes | डिफ़ॉल्ट scaffold |

किसी host ऐप पर plugin:

```php
'extends' => ['com_pinoox_manager'],
```

आपका plugin केवल तभी boot होता है जब host boot होता है (global से हल्का)।

---

## बुनियादी boot.php

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
