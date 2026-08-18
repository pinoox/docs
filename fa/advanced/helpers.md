# توابع کمکی سراسری (Helpers)

[← بازگشت به فهرست](../README.md)

پینوکس ۳.x helperهای global در `pincore/functions/` بارگذاری می‌کند. برای توسعه اپ روزمره همین توابع (+ Portal) کافی است — Component هسته را مستقیم نسازید.

---

## helperهای اصلی

| Helper | کاربرد | مثال |
|--------|--------|------|
| `render()` | HTML به string | `$html = render('email', $data);` |
| `response()` | پاسخ HTTP | `return response()->json($data);` |
| `redirect()` | ریدایرکت | `return redirect(url('login'));` |
| `url()` | URL اپ/سایت | `url('products')` |
| `path()` | مسیر فایل روی دیسک | `path('storage/logs/app.log')` |
| `assets()` | URL فایل theme | `assets('dist/app.css')` |
| `config()` | خواندن/نوشتن config | `config('app.name')` |
| `t()` | ترجمه (return) | `t('welcome.title')` |
| `lang()` | ترجمه (echo) | `lang('welcome.title')` |
| `app()` | اپ فعال | `app()->get('package')` |
| `auth()` | کاربر لاگین‌شده | `auth()` → `Auth::user()` |
| `user()` | فیلد کاربر | `user('email')` |
| `isLogin()` | وضعیت ورود | `if (isLogin()) { … }` |
| `session()` | session | `session('token')` |
| `runtime()` | kernel HTTP جاری | `runtime()->getRequest()` |
| `_env()` | متغیر محیط | `_env('APP_DEBUG', false)` |
| `alias()` | Flow/class alias | `alias('auth')` |
| `jalali()`, `gregorian()`, `date_display()` | تاریخ / تقویم | `date_display($time, 'datetime')` |
| `event()`, `event_listen()`, `event_has()`, `event_fake()` | ارسال / گوش‌دادن / تست رویداد | `event('order.register', ['id' => 12])` |

برای رندر HTML در کنترلر از **`View::render()`** استفاده کنید (مثل اپ‌های سیستمی). تابع `view()` هم وجود دارد اما در کنترلر Portal را ترجیح دهید.

---

## Request — تزریق یا `runtime()`

helper سراسری `request()` در pincore وجود ندارد. در Controller و Component از type-hint استفاده کنید:

```php
use Pinoox\Component\Http\Request;

public function save(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    $email = $request->requestOne('email');
    $all = $request->all();
}
```

در Flow یا جایی که signature اجازه نمی‌دهد:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`، `user()`، Flow

```php
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

$email = auth('email');

// app.php → 'alias' => ['auth' => AuthFlow::class]
// routes → ->flows(['auth']) یا group با flows
```

---

## View و Response

```php
use Pinoox\Portal\View;

return View::render('pages/list', ['items' => $items]);

return response()->json(['ok' => true]);

return redirect(url('dashboard'));
```

---

## Config

```php
$enabled = config('payment.enabled', false);

config('payment')->set('enabled', true)->save();
```

---

## Lang

```php
$label = t('product.title');
// در Twig: {{ t('product.title') }}
```

---

## URL و Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## رویدادها

```php
event('order.register', ['id' => 12, 'user_id' => 4]);
event_listen('order.register', function ($event) {
    $id = $event->get('id');
});

OrderPlaced::dispatch($orderId, $email);
event_listen(function (OrderPlaced $event) {
    // type-hint کافی است
});
```

راهنمای کامل: [رویدادها](./events.md).

---

## تاریخ و تقویم

```php
use Pinoox\Portal\Date;

$label = Date::display($order->created_at, 'datetime');
$jalali = jformat($order->paid_at, 'Y/m/d H:i');
```

با `'date' => 'jalali'` در `app.php`، `Date::display()` و `date_smart()` خودکار از تقویم اپ استفاده می‌کنند. جزئیات: [تاریخ و تقویم](../basic/date-and-calendar.md).

---

## helperهای اپ سفارشی

در `app.php`:

```php
'loader' => [
    '@func' => 'func.php',
],
```

```php
// apps/com_acme_shop/func.php
function format_price(float $amount): string
{
    return number_format($amount) . ' تومان';
}
```

---

## Twig helpers (در قالب)

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## نکات

- در کنترلر برای HTML از `View::render()` استفاده کنید؛ helperهای دیگر (`url()`, `t()`, …) برای کار روزمره کافی‌اند.
- helperها فقط بعد از bootstrap پینوکس کار می‌کنند — در اسکریپت خام PHP خارج از `index.php`/`pinoox` لود نشوند.
- برای منطق پیچیده helper نسازید؛ `Component/` + Portal بهتر است.

---

## مستندات مرتبط

- [Portal — پورتال](../basic/portal.md)
- [URL — آدرس](../basic/url.md)
- [مسیر — Path](../basic/path.md)
- [زبان](../basic/language.md)
- [رویدادها (Events)](./events.md)
- [سرویس‌ها](services.md)

---

[← بازگشت به فهرست](../README.md)
