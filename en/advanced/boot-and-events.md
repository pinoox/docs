# boot.php and events

[← Back to index](../../readme.md)

Besides `routes/`, you can register routes, API endpoints, flows, schedules, and listeners in **`boot.php`** — useful for **plugins**, micro-modules, or hooks into a host app (e.g. manager).

---

## Three app modes

| Mode | Config | Behavior |
|------|--------|----------|
| **Route only** | `router.routes` only | Runs when the app URL is active |
| **Boot global** | `boot-global => true` | Boots on every HTTP request |
| **Boot + Route** | `boot.php` + routes | Default scaffold |

Plugin on a host app:

```php
'extends' => ['com_pinoox_manager'],
```

Your plugin boots only when the host boots (lighter than global).

---

## Basic boot.php

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

## AppRegister — common methods

| Method | Purpose |
|--------|---------|
| `web(callable)` | Register routes via builder |
| `route([...])` | Single web route |
| `api([manifest])` | Full API manifest |
| `apiRoute([...])` | Single API endpoint |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow alias |
| `schedule(callable)` | Scheduled task |
| `listen('event', listener)` | Event listener |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | Hook when another app boots |

---

## Event portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

See [Email](./mail.md) for decoupling mail from controllers.

**Flow** = before the controller (middleware). **Event** = after an action (side effects).

---

## Helpers

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot cache

`'boot' => true` under `cache.stores` in `app.php` bakes boot via Pinker — see [Pinker](./pinker.md).

---

## Related docs

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Routers](../basic/routers.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../../readme.md)
