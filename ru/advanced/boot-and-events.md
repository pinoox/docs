# boot.php и события (events)

[← Назад к оглавлению](../README.md)

Помимо `routes/`, вы можете регистрировать маршруты, API-эндпоинты, flow, расписания и слушатели событий в **`boot.php`** — это полезно для **плагинов**, микромодулей или хуков в хост-приложение (например, manager).

Каждое приложение может иметь `apps/{package}/boot.php`. Файл возвращает closure с `AppRegister` и выполняется **до** обработки запроса.

---

## Жизненный цикл

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### Этапы pipeline

| Этап | Назначение |
|------|------------|
| `boot.global` | Boot приложений с `boot-global => true` на каждый запрос |
| `app.boot` | Boot активного route-приложения (+ extenders через `extends`) |

### События boot

| Имя | Когда |
|-----|-------|
| `app.booting` / `app.booting.{package}` | До commit |
| `app.booted` / `app.booted.{package}` | После integrate |
| `app.routes` / `app.routes.{package}` | При применении web-маршрутов |
| `app.api` / `app.api.{package}` | При построении API registry |

Слушать из `boot.php`:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### События запроса (ядро)

Автоматически на каждый HTTP-запрос (`AppCoreEventSubscriber`):

| Имя | Когда | Вариант package | Именованный канал |
|-----|-------|-----------------|-------------------|
| `app.route.matched` | После match маршрута | `app.route.matched.{package}` | `app.route.{routeName}` или `app.api.{routeName}` |
| `app.controller` | Перед controller | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | Перед отправкой ответа | `app.response.{package}` | — |
| `app.exception` | Необработанное исключение | `app.exception.{package}` | — |
| `app.terminate` | После отправки ответа | `app.terminate.{package}` | — |

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

Для простых hook — **watches** (`onRoute`, `onApi`, …); для полного контроля — **listen** на события ядра.

---

## Три режима приложения

| Режим | Конфигурация | Поведение |
|------|--------|----------|
| **Route only** | только `router.routes` | Выполняется, когда URL приложения активен |
| **Boot global** | `boot-global => true` | Загружается на каждый HTTP-запрос |
| **Boot + Route** | `boot.php` + маршруты | Шаблон по умолчанию |

Плагин для хост-приложения:

```php
'extends' => ['com_host_app'],
```

Ваш плагин загружается только тогда, когда загружается хост (легче, чем global).

---

## ключи `app.php` для boot

Эти ключи в `apps/{package}/app.php` управляют **запуском** `boot.php`, **моментом** запуска и кэшированием. Они настраивают pipeline boot — не заменяют сам `boot.php`.

### Файл boot (`boot`)

| Значение | По умолчанию | Эффект |
|----------|--------------|--------|
| `true` | да | Выполнять `boot.php` при boot приложения |
| `false` | | Без boot — только маршруты |
| `'path/custom.php'` | | Другой файл относительно корня app |

Файл должен **возвращать callable** `fn (AppRegister $register) => …`.

### Глобальный plugin (`boot-global`)

| Значение | По умолчанию | Эффект |
|----------|--------------|--------|
| `false` | да | Boot только когда app активен |
| `true` | | Boot при **каждом HTTP-запросе** |

### Plugin на host (`extends`)

| Значение | По умолчанию | Эффект |
|----------|--------------|--------|
| `[]` | да | Обычное приложение |
| `['com_host_app']` | | Boot **до** host при его активации |

### Доп. регистрация (`startup`)

Optional callable в `app.php` **после** `boot.php`.

### Кэш boot (`cache`)

Opt-in: `cache.enabled` = `true`. После деплоя: `php pinoox cache:build {package}`.

### Быстрый выбор

| Цель | Настройка |
|------|-----------|
| Обычное app | `'boot' => true` |
| Только routes | `'boot' => false` |
| Глобальный plugin | `'boot-global' => true` |
| Plugin на host | `'extends' => ['com_host_app']` |

---

## Базовый boot.php

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
