# boot.php и события (events)

[← Назад к оглавлению](../README.md)

Помимо `routes/`, вы можете регистрировать маршруты, API-эндпоинты, flow, расписания и слушатели событий в **`boot.php`** — это полезно для **плагинов**, микромодулей или хуков в хост-приложение (например, manager).

---

## Три режима приложения

| Режим | Конфигурация | Поведение |
|------|--------|----------|
| **Route only** | только `router.routes` | Выполняется, когда URL приложения активен |
| **Boot global** | `boot-global => true` | Загружается на каждый HTTP-запрос |
| **Boot + Route** | `boot.php` + маршруты | Шаблон по умолчанию |

Плагин для хост-приложения:

```php
'extends' => ['com_pinoox_manager'],
```

Ваш плагин загружается только тогда, когда загружается хост (легче, чем global).

---

## Базовый boot.php

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

## AppRegister — основные методы

| Метод | Назначение |
|--------|---------|
| `web(callable)` | Регистрация маршрутов через билдер |
| `route([...])` | Одиночный web-маршрут |
| `api([manifest])` | Полный API-манифест |
| `apiRoute([...])` | Одиночный API-эндпоинт |
| `action('name', handler)` | Именованный Action |
| `flowAlias(['auth' => AuthFlow::class])` | Алиас flow |
| `schedule(callable)` | Запланированная задача |
| `listen('event', listener)` | Слушатель события |
| `subscribe(SubscriberClass::class)` | Подписчик Symfony |
| `when('com_host', fn)` | Хук на загрузку другого приложения |

---

## Портал Event

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

См. [Почта (Email)](./mail.md) о том, как отделить отправку почты от контроллеров.

**Flow** = до контроллера (middleware). **Event** = после действия (побочные эффекты).

---

## Хелперы

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Кэш загрузки (boot cache)

`'boot' => true` в разделе `cache.stores` файла `app.php` «запекает» загрузку через Pinker — см. [Pinker](./pinker.md).

---

## Связанные документы

- [Расписание (Schedule)](./schedule.md)
- [Flows](../basic/flows.md)
- [Роутеры](../basic/routers.md)
- [Структура проекта](../start/structure.md)

---

[← Назад к оглавлению](../README.md)
