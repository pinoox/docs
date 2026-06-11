# boot.php and events

[← 색인으로 돌아가기](../README.md)

`routes/` 외에 **`boot.php`**에서 route, API endpoint, Flow, schedule, listener를 등록할 수 있습니다 — **plugin**, micro-module, host app(예: manager) hook에 유용합니다.

---

## 세 가지 앱 mode

| Mode | Config | Behavior |
|------|--------|----------|
| **Route only** | `router.routes` only | 앱 URL이 활성일 때 실행 |
| **Boot global** | `boot-global => true` | 모든 HTTP request에서 boot |
| **Boot + Route** | `boot.php` + routes | Default scaffold |

Host app의 plugin:

```php
'extends' => ['com_pinoox_manager'],
```

plugin은 host가 boot할 때만 boot (global보다 가벼움).

---

## 기본 boot.php

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

## AppRegister — 자주 쓰는 method

| Method | Purpose |
|--------|---------|
| `web(callable)` | builder로 route 등록 |
| `route([...])` | Single web route |
| `api([manifest])` | Full API manifest |
| `apiRoute([...])` | Single API endpoint |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | Flow alias |
| `schedule(callable)` | Scheduled task |
| `listen('event', listener)` | Event listener |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | 다른 앱 boot 시 hook |

---

## Event portal

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Controller에서 mail 분리는 [Email](./mail.md) 참조.

**Flow** = Controller 전 (미들웨어). **Event** = action 후 (side effect).

---

## Helper

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Boot cache

`app.php`의 `cache.stores` 아래 `'boot' => true`가 Pinker로 boot를 bake — [Pinker](./pinker.md) 참조.

---

## 관련 문서

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Router](../basic/routers.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)
