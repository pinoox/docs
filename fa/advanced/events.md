# رویدادها (Events)

[← بازگشت به فهرست](../README.md)

Event کار **چه اتفاقی افتاد** را از **بعدش چه کار شود** جدا می‌کند. کنترلر سفارش را ثبت می‌کند؛ listener ایمیل می‌فرستد، موجودی را کم می‌کند یا به ادمین خبر می‌دهد — بدون اینکه کنترلر از آن‌ها خبر داشته باشد.

**Flow** = قبل از کنترلر (middleware، می‌تواند درخواست را قطع کند). **Event** = بعد از عمل (side effect).

برای hook روی درخواست از **watch** استفاده کنید (`onRoute`, `onModel`, …). برای عمل دامنه از **event** (`order.register`, `OrderPlaced`).

مسیرها در این صفحه مربوط به اپ پلتفرم است (`apps/com_acme_shop/`). در پروژهٔ Pinx تک‌اپ همان پوشه‌ها در ریشهٔ پروژه هستند (`Event/`, `Listener/`) و namespace می‌شود `App\Event` / `App\Listener`.

---

## کدام روش؟

| هدف | کار |
|-----|-----|
| هوک سریع، بدون کلاس | نام رشته‌ای: `event('order.register', ['id' => 12])` |
| payload تایپ‌شده + listener خودکار | کلاس در `Event/` + `Listener/` (پیش‌فرض، صفر کانفیگ) |
| اضافه / افزونه / اپ میزبان | `boot.php` → `$register->listen(...)` |
| map صریح، بدون اسکن پوشه | `app.php` → `'events' => ['discover' => false, 'listen' => [...]]` |

هر سه با هم کار می‌کنند: اول discovery، بعد `events.listen` در `app.php`، بعد `boot.php`.

---

## رویداد نام‌دار (بدون کلاس)

```php
event_listen('order.register', function ($event) {
    $id = $event->get('id');       // 12
    $userId = $event->id;          // همان، از __get
    $userId = $event['user_id'];   // ArrayAccess
    $name = $event->name();        // order.register
});

event('order.register', ['id' => 12, 'user_id' => 4]);
```

آبجکت از نوع `Pinoox\Component\Event\NamedEvent` است. شکل payload:

| فراخوانی | خواندن |
|----------|--------|
| `event('order.register', ['id' => 12])` | `$event->id` / `$event->get('id')` |
| `event('order.paid', 99, 'ok')` | `$event->get(0)` و `$event->get(1)` |
| `event('order.note', 'hello')` | `$event->data` |
| `event('order.register')` | payload خالی |

از `boot.php`:

```php
use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Event\NamedEvent;

return function (AppRegister $register): void {
    $register->listen('order.register', function (NamedEvent $event): void {
        // $event->get('id')
    });
};
```

اگر آرگومان اول `event()` **نام یک کلاس موجود** باشد، پینوکس همان کلاس را می‌سازد (رویداد تایپ‌شده در بخش بعد). نام‌های نقطه‌دار مثل `order.register` کلاس نیستند و همیشه `NamedEvent` می‌شوند.

---

## Event و Listener تایپ‌شده (پیش‌فرض)

فایل را در `Event/` و `Listener/` بگذارید. pipeline بوت آن‌ها را خودکار پیدا می‌کند. نیازی به خط در `boot.php` یا Provider نیست.

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
        // ارسال ایمیل با $event->orderId و $event->email
    }
}
```

از کنترلر:

```php
use App\com_acme_shop\Event\OrderPlaced;

OrderPlaced::dispatch($order->id, $order->email);

// معادل:
event(new OrderPlaced($order->id, $order->email));
event(OrderPlaced::class, $order->id, $order->email);
```

نام dispatcher پیش‌فرض همان `::class` است. نام کوتاه اختیاری:

```php
class OrderPlaced extends Event
{
    public static $eventName = 'shop.order.placed';
}
```

`dispatch()` و `listen(OrderPlaced::class, …)` هر دو به همان رشته resolve می‌شوند.

### شلیک شرطی

```php
OrderPlaced::dispatchIf($paid, $order->id, $order->email);
OrderPlaced::dispatchUnless($draft, $order->id, $order->email);
```

---

## قرارداد Listener

auto-discovery این‌ها را ثبت می‌کند:

1. **`handle(EventClass $event)`** — یک event برای هر کلاس
2. **`__invoke(EventClass $event)`** — listener فراخوان‌پذیر
3. **متدهای `handle*`** با type-hint آرگومان اول
4. **`#[ListensTo]`** روی کلاس یا متد
5. **union type** — `handle(OrderPlaced|PaymentConfirmed $event)`
6. **`EventSubscriberInterface`** — subscriber سیمفونی

```php
use App\com_acme_shop\Event\OrderPlaced;
use App\com_acme_shop\Event\PaymentConfirmed;
use Pinoox\Support\Event\ListensTo;

class OrderListener
{
    public function handleOrderPlaced(OrderPlaced $event): void
    {
        // موجودی
    }

    #[ListensTo(PaymentConfirmed::class)]
    public function onPaid(PaymentConfirmed $event): void
    {
        // وضعیت سفارش
    }

    #[ListensTo(OrderPlaced::class, priority: 10)]
    public function notifyAdmin(OrderPlaced $event): void
    {
        // قبل از listenerهای اولویت پیش‌فرض
    }
}
```

Attribute روی کلاس (متد `handle` یا `__invoke`):

```php
#[ListensTo(OrderPlaced::class)]
class SendOrderEmail
{
    public function handle(OrderPlaced $event): void {}
}
```

کلاس abstract، interface، و listenerهایی که constructor اجباری دارند اسکن نمی‌شوند (در `boot.php` خودتان بسازید).

---

## Helperها

| Helper | کار |
|--------|-----|
| `event($event, ...$payload)` | شلیک کلاس، instance، یا نام رشته‌ای |
| `event_listen($event, $listener = null, $priority = 0)` | ثبت listener (کلاس، `[Class, 'method']`، یا closure) |
| `event_subscribe($subscriber)` | subscriber سیمفونی (کلاس یا instance) |
| `event_has($event)` | آیا برای این نام/کلاس listener هست |
| `event_name($event)` | تبدیل کلاس / instance / رشته به نام dispatcher |
| `event_fake($events = null)` | تست: شلیک‌ها را ضبط می‌کند، listener اجرا نمی‌شود |

closure می‌تواند نام event را ندهد اگر پارامتر اول type-hint شده باشد:

```php
event_listen(function (OrderPlaced $event) {
    // روی OrderPlaced ثبت می‌شود
});
```

Portal (همان dispatcher):

```php
use Pinoox\Portal\Event;

Event::listen(OrderPlaced::class, SendOrderEmail::class);
Event::listen('order.register', $listener);
Event::dispatch($event, $name);
Event::hasListeners(event_name(OrderPlaced::class));
```

---

## کاستوم در `app.php`

پیش‌فرض `'events' => true` است (اسکن `Listener/`). لازم نیست بنویسید.

```php
'events' => true,    // auto-discover (پیش‌فرض)
'events' => false,   // بدون discovery و بدون map
                     // (listenerهای boot.php همچنان کار می‌کنند)

'events' => [
    'discover' => true,           // اسکن Listener/ (پیش‌فرض true)
    'path' => 'Listener',         // یا ['Listener', 'Domain/Orders/Listeners']
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

## کاستوم در `boot.php`

برای listener اضافه، افزونه، و هوک روی اپ میزبان از `AppRegister` استفاده کنید. discovery قبل از آن اجرا شده است.

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use App\com_acme_shop\Event\OrderPlaced;
use App\com_acme_shop\Listener\SendOrderEmail;

return function (AppRegister $register): void {
    $register->listen(OrderPlaced::class, SendOrderEmail::class);

    $register->listen(function (OrderPlaced $event): void {
        // side effect اضافه
    });

    $register->listen('order.register', function ($event): void {
        // رویداد نام‌دار
    });

    $register->subscribe(InventorySubscriber::class);
};
```

`startup` در `app.php` همان API را دارد و بعد از `boot.php` اجرا می‌شود.

---

## Subscriber

یک کلاس برای چند event — `EventSubscriberInterface` سیمفونی (کمک `Subscriber` پینوکس متد `listeners()` را map می‌کند):

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

کلاس را در `Listener/` بگذارید (خودکار subscribe می‌شود) یا با `'events.subscribe'` / `$register->subscribe(...)` / `event_subscribe(...)` ثبت کنید.

---

## تست

```php
use App\com_acme_shop\Event\OrderPlaced;
use Pinoox\Portal\Event;

it('در checkout رویداد OrderPlaced را شلیک می‌کند', function () {
    event_fake(OrderPlaced::class);

    // … اجرای checkout …

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

`event_fake()` بدون آرگومان **همه** eventها را بعد از آن فراخوانی جعل می‌کند (از جمله رویدادهای فریمورک). بهتر است لیست کلاس یا نام بدهید. در پایان تست `Event::dontFake()` را صدا بزنید.

تا وقتی fake فعال است listener اجرا نمی‌شود — خود listener را با صدا زدن `handle()` تست کنید.

---

## رویدادهای هستهٔ درخواست و بوت

کرنل در هر درخواست HTTP رویدادهای چرخهٔ عمر را شلیک می‌کند. از `boot.php` گوش دهید (جزئیات: [boot.php و رویدادها](./boot-and-events.md)):

| نام | زمان |
|-----|------|
| `app.booted` / `app.booted.{package}` | بعد از integrate اپ |
| `app.route.matched` | match شدن route |
| `app.controller` | قبل از کنترلر |
| `app.response` | قبل از ارسال پاسخ |
| `app.exception` | exception مهارنشده |
| `app.terminate` | بعد از ارسال پاسخ |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->routeName()
});
```

برای هوک ساده روی route/API/model به‌جای کلاس Event از `$register->onRoute()` / `onApi()` / `onModel()` استفاده کنید.

---

## مثال فروشگاه (سر تا ته)

**۱. ثبت سفارش** — کنترلر فقط شلیک می‌کند:

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

**۲. listener نام‌دار** (بدون فایل کلاس، اگر در `boot.php` ثبت شود):

```php
event_listen('order.register', function ($event) {
    // لاگ، وب‌هوک، …
    $id = $event->get('id');
});
```

**۳. listener تایپ‌شده** — `Listener/SendOrderEmail.php` با `handle(OrderPlaced $event)` خودکار کشف می‌شود.

---

## مستندات مرتبط

- [boot.php و رویدادها](./boot-and-events.md) — pipeline بوت، watch، رویدادهای هسته، lifecycle.php
- [ارسال ایمیل](./mail.md) — ایمیل از داخل listener
- [توابع کمکی سراسری](./helpers.md)
- [مرجع app.php](../start/app-manifest.md)
- [ساختار پوشه‌بندی](../start/structure.md)
- [Flow](../basic/flows.md)
- [تست](../test/getting-started.md)
- [Mocking](../test/mocking.md)

---

[← بازگشت به فهرست](../README.md)
