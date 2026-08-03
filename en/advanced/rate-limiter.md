# Rate Limiter

[← Back to index](../README.md)

Pinoox includes a first-class **Rate Limiter** for throttling work across the framework — HTTP routes, Flows, controllers, jobs, CLI, and scheduled tasks. Counters use **`Pinoox\Portal\Cache`** (file, Redis, or any PSR-16 driver). Nothing talks to Redis directly.

> Prefer **`Pinoox\Portal\RateLimiter`** and **`Pinoox\Component\RateLimiter\Limit`**. On routes, use the **`throttle:`** Flow alias.

---

## Overview

| Piece | Role |
|-------|------|
| `RateLimiter` portal | `define`, `attempt`, `hit`, `clear`, `remaining`, `availableIn`, `tooManyAttempts` |
| `Limit` | Fluent window builder (`perMinute`, `perHour`, …) |
| `ThrottleFlow` | HTTP Flow — alias `throttle:api` |
| Cache store | Fixed-window counters via `Portal\Cache` |

---

## Defining limiters

Register named limiters early (app `boot.php`, a service, or a Flow `before`):

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

### Limit builders

```php
Limit::perSecond(5);
Limit::perMinute(60);
Limit::perMinutes(5, 20);   // 20 attempts / 5 minutes
Limit::perHour(500);
Limit::perDay(5000);
Limit::none();              // skip throttling for this request
```

Chain options:

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

A define callback may return one `Limit`, `Limit::none()`, or an **array** of limits (all must pass).

---

## Flow integration

The core alias **`throttle`** is registered by default. Pass the limiter name after a colon:

```php
use function Pinoox\Router\{post, group};

post('login', [AuthController::class, 'login'])
    ->flow('throttle:login')
    ->name('login');

group(['prefix' => 'api', 'flows' => ['auth', 'throttle:api']], function () {
    // ...
});
```

Equivalent forms:

```php
use Pinoox\Flow\ThrottleFlow;

->flow('throttle:api')
->flow([ThrottleFlow::for('api')])
->flow([new ThrottleFlow('api')])
```

Nested Pinoox-style aliases still work if you prefer dots:

```php
// app.php
'alias' => [
    'throttle' => [
        'api' => ThrottleFlow::for('api'),
    ],
],

// route
->flow('throttle.api')
```

When the limit is exceeded, `ThrottleFlow` returns **HTTP 429** and does not call the controller.

### Default 429 body

```json
{
    "message": "Too Many Requests."
}
```

Headers (when applicable):

| Header | Meaning |
|--------|---------|
| `Retry-After` | Seconds until the window resets |
| `X-RateLimit-Limit` | Max attempts in the window |
| `X-RateLimit-Remaining` | Attempts left |

Override via `->message()`, `->status()`, `->headers()`, or `->response(...)`.

---

## Manual usage (no HTTP)

Safe in jobs, events, CLI, and services:

```php
use Pinoox\Portal\RateLimiter;

$key = 'send-email:' . $user->id;

if (RateLimiter::tooManyAttempts($key, 10)) {
    $seconds = RateLimiter::availableIn($key);
    // wait or skip
    return;
}

RateLimiter::hit($key, 60);

// or wrap work:
$result = RateLimiter::attempt('invoice:' . $invoice->id, 3, function () {
    return $this->generateInvoice();
}, decaySeconds: 60);

if ($result === false) {
    // rate limited
}
```

Other helpers: `remaining()`, `retriesLeft()`, `attempts()`, `clear()` / `resetAttempts()`.

---

## Cache drivers

Counters use whatever **`Portal\Cache`** is configured (`config/cache.config.php`):

- File (default)
- Redis
- Any future PSR-16 store bound to the Cache portal

Optional prefix (`config/rate_limiter.config.php` or env):

```php
// pincore/config/rate_limiter.config.php
return [
    'prefix' => env('RATE_LIMITER_PREFIX', 'pinoox_rate:'),
];
```

```env
RATE_LIMITER_PREFIX=pinoox_rate:
CACHE_DRIVER=redis
```

---

## Examples

### Protect login

```php
RateLimiter::define('login', fn ($request) =>
    Limit::perMinute(5)->by($request->getClientIp())
);

post('login', [AuthController::class, 'login'])->flow('throttle:login');
```

### API by user id

```php
RateLimiter::define('api', function ($request) {
    return Limit::perMinute(120)->by(
        Auth::check() ? (string) Auth::id() : $request->getClientIp()
    );
});
```

### Skip limit for trusted clients

```php
RateLimiter::define('api', function ($request) {
    if (in_array($request->getClientIp(), config('app.trusted_ips', []), true)) {
        return Limit::none();
    }

    return Limit::perMinute(60)->by($request->getClientIp());
});
```

### Queue / CLI

```php
if (!RateLimiter::attempt('report:' . $tenantId, 1, fn () => $this->buildReport(), 3600)) {
    $this->info('Report already generated this hour.');
}
```

---

## Best practices

1. **Key by identity** — prefer user id when authenticated; fall back to IP.
2. **Name limiters clearly** — `login`, `api`, `upload` map 1:1 to `throttle:…`.
3. **Clear on success when it makes sense** — e.g. after a successful login: `RateLimiter::clear('login|'.$ip)`.
4. **Use Redis in production** for multi-server apps (same Cache portal config).
5. **Keep define() out of the hot path** — register once at boot, not inside every request handler.
6. **Do not rely on rate limits alone for security** — combine with Auth, validation, and permissions.

---

## Related docs

- [Flow](../basic/flows.md)
- [Router](../basic/routers.md)
- [Pinker and cache](./pinker.md)
- [HTTP client](./http-client.md)

---

[← Back to index](../README.md)
