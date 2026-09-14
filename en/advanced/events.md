# Events

[← Back to index](../README.md)

Events decouple **what happened** from **what to do next**. A controller places an order; listeners send email, update stock, or notify admin — without the controller knowing about any of them.

**Flow** = before the controller (middleware, can block). **Event** = after an action (side effects).

Use **watches** (`onRoute`, `onModel`, …) for request hooks. Use **events** for domain actions (`order.register`, `OrderPlaced`).

Paths below use a platform app (`apps/com_acme_shop/`). In a Pinx single-app project the same folders live at the project root (`Event/`, `Listener/`) and the namespace is `App\Event` / `App\Listener`.

---

## Choose a style

| You want… | Do this |
|-----------|---------|
| Fast hook, no classes | Named string: `event('order.register', ['id' => 12])` |
| Typed payload, auto-wired listeners | Classes in `Event/` + `Listener/` (default, zero config) |
| Extra / plugin / host app | `boot.php` → `$register->listen(...)` |
| Explicit map, no folder scan | `app.php` → `'events' => ['discover' => false, 'listen' => [...]]` |

All three work together: discovery runs first, then `app.php` `events.listen`, then `boot.php`.

---

## Named events (no classes)

```php
event_listen('order.register', function ($event) {
    $id = $event->get('id');       // 12
    $userId = $event->id;          // same via __get
    $userId = $event['user_id'];   // ArrayAccess
    $name = $event->name();        // order.register
});

event('order.register', ['id' => 12, 'user_id' => 4]);
```

The object is `Pinoox\Component\Event\NamedEvent`. Payload shapes:

| Call | Read |
|------|------|
| `event('order.register', ['id' => 12])` | `$event->id` / `$event->get('id')` |
| `event('order.paid', 99, 'ok')` | `$event->get(0)`, `$event->get(1)` |
| `event('order.note', 'hello')` | `$event->data` |
| `event('order.register')` | empty payload |

From `boot.php`:

```php
use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Event\NamedEvent;

return function (AppRegister $register): void {
    $register->listen('order.register', function (NamedEvent $event): void {
        // $event->get('id')
    });
};
```

If the first argument to `event()` is an **existing class name**, Pinoox instantiates that class (typed events below). Dotted names like `order.register` are never classes, so they always become `NamedEvent`.

---

## Typed events and listeners (default)

Drop files in `Event/` and `Listener/`. The app boot pipeline auto-discovers them. No `boot.php` line and no provider required.

```
apps/com_acme_shop/
├── Event/
│   └── OrderPlaced.php
└── Listener/
    └── SendOrderEmail.php
```

```php
<?php
// Event/OrderPlaced.php
namespace App\com_acme_shop\Event;

use Pinoox\Component\Event\Event;

class OrderPlaced extends Event
{
    public function __construct(
        public readonly int $orderId,
        public readonly string $email,
    ) {}
}
```

```php
<?php
// Listener/SendOrderEmail.php
namespace App\com_acme_shop\Listener;

use App\com_acme_shop\Event\OrderPlaced;

class SendOrderEmail
{
    public function handle(OrderPlaced $event): void
    {
        // send mail using $event->orderId, $event->email
    }
}
```

Dispatch from a controller:

```php
use App\com_acme_shop\Event\OrderPlaced;

OrderPlaced::dispatch($order->id, $order->email);

// same:
event(new OrderPlaced($order->id, $order->email));
event(OrderPlaced::class, $order->id, $order->email);
```

The dispatcher name defaults to the class name. Optional friendly name:

```php
class OrderPlaced extends Event
{
    public static $eventName = 'shop.order.placed';
}
```

`dispatch()` and `listen(OrderPlaced::class, …)` both resolve to that string, so they stay in sync.

### Conditional dispatch

```php
OrderPlaced::dispatchIf($paid, $order->id, $order->email);
OrderPlaced::dispatchUnless($draft, $order->id, $order->email);
```

---

## Listener conventions

Auto-discovery registers:

1. **`handle(EventClass $event)`** — one event per class
2. **`__invoke(EventClass $event)`** — invokable listener
3. **`handle*` methods** whose first argument is type-hinted (Laravel-style)
4. **`#[ListensTo]`** on a class or method
5. **Union types** — `handle(OrderPlaced|PaymentConfirmed $event)`
6. **`EventSubscriberInterface`** — subscribed as a Symfony subscriber

```php
use App\com_acme_shop\Event\OrderPlaced;
use App\com_acme_shop\Event\PaymentConfirmed;
use Pinoox\Support\Event\ListensTo;

class OrderListener
{
    public function handleOrderPlaced(OrderPlaced $event): void
    {
        // inventory
    }

    #[ListensTo(PaymentConfirmed::class)]
    public function onPaid(PaymentConfirmed $event): void
    {
        // status
    }

    #[ListensTo(OrderPlaced::class, priority: 10)]
    public function notifyAdmin(OrderPlaced $event): void
    {
        // runs before default-priority listeners
    }
}
```

Class-level attribute (single handler method `handle` / `__invoke`):

```php
#[ListensTo(OrderPlaced::class)]
class SendOrderEmail
{
    public function handle(OrderPlaced $event): void {}
}
```

Abstract classes, interfaces, and listeners whose constructor requires arguments are skipped (instantiate them yourself in `boot.php`).

---

## Helpers

| Helper | Purpose |
|--------|---------|
| `event($event, ...$payload)` | Dispatch a class, instance, or named string |
| `event_listen($event, $listener = null, $priority = 0)` | Register a listener (class, `[Class, 'method']`, or closure) |
| `event_subscribe($subscriber)` | Symfony subscriber class or instance |
| `event_has($event)` | Whether listeners exist for that name/class |
| `event_name($event)` | Resolve class / instance / string to the dispatcher name |
| `event_fake($events = null)` | Tests: record dispatches, do not run listeners |

Closure listeners can omit the event name when the first parameter is type-hinted:

```php
event_listen(function (OrderPlaced $event) {
    // registered on OrderPlaced automatically
});
```

Portal (same dispatcher):

```php
use Pinoox\Portal\Event;

Event::listen(OrderPlaced::class, SendOrderEmail::class);
Event::listen('order.register', $listener);
Event::dispatch($event, $name);
Event::hasListeners(event_name(OrderPlaced::class));
```

---

## Customize in `app.php`

Default is `'events' => true` (discover `Listener/`). You do not need to set it.

```php
'events' => true,    // auto-discover (default)
'events' => false,   // skip discovery and the listen/subscribe map
                     // (boot.php listeners still work)

'events' => [
    'discover' => true,           // scan Listener/ (default true)
    'path' => 'Listener',         // or ['Listener', 'Domain/Orders/Listeners']
    'listen' => [
        OrderPlaced::class => [
            SendOrderEmail::class,
            [NotifyAdmin::class, 'onPlaced'],
        ],
        'order.register' => [LogOrder::class],
    ],
    'subscribe' => [
        InventorySubscriber::class,
    ],
],
```

---

## Customize in `boot.php`

Use `AppRegister` for extra listeners, plugins, and host-app hooks. Discovery still runs first.

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use App\com_acme_shop\Event\OrderPlaced;
use App\com_acme_shop\Listener\SendOrderEmail;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::class, SendOrderEmail::class);

    $register->listen(function (OrderPlaced $event): void {
        // extra side effect
    });

    $register->listen('order.register', function ($event): void {
        // named event
    });

    $register->subscribe(InventorySubscriber::class);
};
```

`app.php` `startup` is the same API, run after `boot.php`.

---

## Subscribers

One class, several events — implement Symfony `EventSubscriberInterface` (Pinoox `Subscriber` helper maps `listeners()`):

```php
namespace App\com_acme_shop\Listener;

use App\com_acme_shop\Event\OrderPlaced;
use App\com_acme_shop\Event\PaymentConfirmed;
use Pinoox\Component\Event\Subscriber;

class InventorySubscriber extends Subscriber
{
    public static function listeners(): array
    {
        return [
            OrderPlaced::eventName() => 'onPlaced',
            PaymentConfirmed::eventName() => 'onPaid',
        ];
    }

    public function onPlaced(OrderPlaced $event): void {}

    public function onPaid(PaymentConfirmed $event): void {}
}
```

Put the class in `Listener/` (auto-subscribed) or register with `'events.subscribe'` / `$register->subscribe(...)` / `event_subscribe(...)`.

---

## Testing

```php
use App\com_acme_shop\Event\OrderPlaced;
use Pinoox\Portal\Event;

it('dispatches OrderPlaced on checkout', function () {
    event_fake(OrderPlaced::class);

    // … run checkout …

    Event::assertDispatched(OrderPlaced::class);
    Event::assertDispatchedOnce(OrderPlaced::class);
    Event::assertDispatched(OrderPlaced::class, function (OrderPlaced $event) {
        return $event->orderId === 12;
    });
    Event::assertNotDispatched(PaymentFailed::class);

    Event::dontFake();
});
```

```php
event_fake('order.register');
event('order.register', ['id' => 1]);
Event::assertDispatched('order.register');
```

`event_fake()` with no arguments fakes **all** events after the call (including framework events). Prefer a class or name list. Always `Event::dontFake()` when the test finishes.

Listeners are not executed while faked — test a listener by calling `handle()` directly.

---

## Core request and boot events

The kernel dispatches lifecycle events on every HTTP request. Listen from `boot.php` (see [boot.php and events](./boot-and-events.md)):

| Name | When |
|------|------|
| `app.booted` / `app.booted.{package}` | After the app is integrated |
| `app.route.matched` | Route matched |
| `app.controller` | Before the controller |
| `app.response` | Before the response is sent |
| `app.exception` | Uncaught exception |
| `app.terminate` | After the response is sent |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->routeName()
});
```

For simple route/API/model hooks, prefer `$register->onRoute()` / `onApi()` / `onModel()` instead of a custom Event class.

---

## Shop example (end to end)

**1. Place the order** — controller only dispatches:

```php
public function checkout(Request $request)
{
    $order = $this->createOrder($request);

    event('order.register', [
        'id' => $order->id,
        'user_id' => $order->user_id,
        'total' => $order->total,
    ]);

    OrderPlaced::dispatch($order->id, $order->email);

    return $this->ok(['order_id' => $order->id]);
}
```

**2. Named listener** (logging, no class file required if registered in `boot.php`):

```php
event_listen('order.register', function ($event) {
    // audit log, webhook, …
    $id = $event->get('id');
});
```

**3. Typed listener** — `Listener/SendOrderEmail.php` with `handle(OrderPlaced $event)` is discovered automatically.

---

## Related docs

- [boot.php and events](./boot-and-events.md) — boot pipeline, watches, core events, lifecycle.php
- [Mail](./mail.md) — send email from a listener
- [Global helpers](./helpers.md)
- [app.php manifest](../start/app-manifest.md)
- [Project structure](../start/structure.md)
- [Flows](../basic/flows.md)
- [Testing](../test/getting-started.md)
- [Mocking](../test/mocking.md)

---

[← Back to index](../README.md)
