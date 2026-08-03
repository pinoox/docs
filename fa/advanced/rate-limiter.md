# Rate Limiter

[← بازگشت به فهرست](../README.md)

پینوکس یک **Rate Limiter** بومی دارد برای محدود کردن کار در کل فریمورک — مسیرهای HTTP، Flow، کنترلر، Job، CLI و زمان‌بندی. شمارنده‌ها از **`Pinoox\Portal\Cache`** استفاده می‌کنند (فایل، Redis یا هر درایور PSR-16). اتصال مستقیم به Redis وجود ندارد.

> ترجیحاً از **`Pinoox\Portal\RateLimiter`** و **`Pinoox\Component\RateLimiter\Limit`** استفاده کنید. روی route از alias فلو **`throttle:`** بهره ببرید.

---

## نمای کلی

| قطعه | نقش |
|------|-----|
| Portal `RateLimiter` | `define`, `attempt`, `hit`, `clear`, `remaining`, `availableIn`, `tooManyAttempts` |
| `Limit` | سازندهٔ روان پنجره زمانی |
| `ThrottleFlow` | فلو HTTP — alias به شکل `throttle:api` |
| Cache store | شمارندهٔ fixed-window روی `Portal\Cache` |

---

## تعریف limiter

نام‌ها را زود ثبت کنید (`boot.php`، سرویس، یا `before` یک Flow):

```php
use Pinoox\Component\Http\Request;
use Pinoox\Component\RateLimiter\Limit;
use Pinoox\Portal\Auth;
use Pinoox\Portal\RateLimiter;

RateLimiter::define('login', function (Request $request) {
    return Limit::perMinute(5)->by($request->getClientIp());
});

RateLimiter::define('api', function (Request $request) {
    return Limit::perMinute(120)->by(
        Auth::id() ?? $request->getClientIp()
    );
});

RateLimiter::define('upload', function (Request $request) {
    return Limit::perMinute(20)->by((string) Auth::id());
});
```

### سازنده‌های Limit

```php
Limit::perSecond(5);
Limit::perMinute(60);
Limit::perMinutes(5, 20);   // ۲۰ تلاش در ۵ دقیقه
Limit::perHour(500);
Limit::perDay(5000);
Limit::none();              // بدون محدودیت برای این درخواست
```

زنجیرهٔ گزینه‌ها:

```php
Limit::perMinute(10)
    ->by($request->getClientIp())
    ->message('Slow down.')
    ->status(429)
    ->headers(['X-Custom' => '1'])
    ->response(fn ($request, $limit, $retryAfter) => response()->json([
        'error' => 'rate_limited',
        'retry_after' => $retryAfter,
    ], 429));
```

خروجی `define` می‌تواند یک `Limit`، `Limit::none()`، یا **آرایه‌ای** از آن‌ها باشد.

---

## اتصال به Flow

alias هستهٔ **`throttle`** به‌صورت پیش‌فرض ثبت شده است. نام limiter را بعد از دو نقطه بگذارید:

```php
use function Pinoox\Router\{post, group};

post('login', [AuthController::class, 'login'])
    ->flow('throttle:login')
    ->name('login');

group(['prefix' => 'api', 'flows' => ['auth', 'throttle:api']], function () {
    // ...
});
```

اشکال معادل:

```php
use Pinoox\Flow\ThrottleFlow;

->flow('throttle:api')
->flow([ThrottleFlow::for('api')])
->flow([new ThrottleFlow('api')])
```

اگر ترجیح می‌دهید از نقطه (الگوی Pinoox) استفاده کنید:

```php
// app.php
'alias' => [
    'throttle' => [
        'api' => ThrottleFlow::for('api'),
    ],
],

->flow('throttle.api')
```

وقتی سقف پر شود، `ThrottleFlow` پاسخ **۴۲۹** می‌دهد و کنترلر اجرا نمی‌شود.

### بدنهٔ پیش‌فرض ۴۲۹

```json
{
    "message": "Too Many Requests."
}
```

هدرها: `Retry-After`, `X-RateLimit-Limit`, `X-RateLimit-Remaining`.

---

## استفادهٔ دستی (بدون HTTP)

مناسب Job، Event، CLI و سرویس:

```php
use Pinoox\Portal\RateLimiter;

$key = 'send-email:' . $user->id;

if (RateLimiter::tooManyAttempts($key, 10)) {
    $seconds = RateLimiter::availableIn($key);
    return;
}

RateLimiter::hit($key, 60);

$result = RateLimiter::attempt('invoice:' . $invoice->id, 3, function () {
    return $this->generateInvoice();
}, decaySeconds: 60);

if ($result === false) {
    // محدود شده
}
```

سایر متدها: `remaining()`, `retriesLeft()`, `attempts()`, `clear()` / `resetAttempts()`.

---

## درایور Cache

شمارنده‌ها همان تنظیمات **`Portal\Cache`** را دنبال می‌کنند (`config/cache.config.php`). پیشوند اختیاری در `config/rate_limiter.config.php` یا env با کلید `RATE_LIMITER_PREFIX`.

---

## بهترین تمرین‌ها

1. با هویت کاربر کلید بزنید؛ در غیر این صورت IP.
2. نام limiter را با `throttle:…` یکی نگه دارید.
3. در صورت نیاز بعد از موفقیت `clear` کنید (مثلاً login موفق).
4. در محیط چندسروره از Redis برای Cache استفاده کنید.
5. `define` را یک‌بار در boot صدا بزنید، نه در هر درخواست.
6. Rate limit جایگزین Auth/Permission نیست.

---

## مستندات مرتبط

- [فلو — Flow](../basic/flows.md)
- [روتر](../basic/routers.md)
- [Pinker و cache](./pinker.md)

---

[← بازگشت به فهرست](../README.md)
