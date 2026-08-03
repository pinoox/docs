# CORS

[← بازگشت به فهرست](../README.md)

پینوکس یک موتور **CORS** بومی دارد. با **Flow** (`cors:api`) یکپارچه است، روی هر `Response` کار می‌کند و به پکیج‌های middleware لاراول وابسته نیست.

> ترجیحاً از **`Pinoox\Portal\Cors`** و **`Pinoox\Component\Cors\CorsPolicy`** استفاده کنید. روی route از alias **`cors:`** بهره ببرید.

---

## نمای کلی

| قطعه | نقش |
|------|-----|
| Portal `Cors` | `define`, `default`, `resolve`, `apply`, `handlePreflight` |
| `CorsPolicy` | سازندهٔ روان سیاست |
| `CorsFlow` | فلو HTTP — `cors:api` |
| `config/cors.config.php` | نام سیاست پیش‌فرض |

---

## شروع سریع

```php
use Pinoox\Component\Cors\CorsPolicy;
use Pinoox\Portal\Cors;

Cors::define('api', function () {
    return CorsPolicy::make()
        ->allowOrigins(['https://example.com', '*.example.com'])
        ->allowMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])
        ->allowHeaders(['*'])
        ->exposeHeaders(['X-RateLimit-Remaining'])
        ->allowCredentials()
        ->maxAge(86400);
});
```

```php
group(['prefix' => 'api', 'flows' => ['cors:api', 'throttle:api']], function () {
    get('/products', [ProductController::class, 'index']);
});
```

---

## سیاست پیش‌فرض

```php
Cors::default(fn () => CorsPolicy::make()
    ->allowOrigins('*')
    ->allowMethods('*')
    ->allowHeaders('*'));

// یا ارجاع به نام:
Cors::default('api');
```

`flow('cors')` و `Cors::apply($response)` از سیاست پیش‌فرض استفاده می‌کنند.

---

## Preflight

درخواست `OPTIONS` با هدر `Access-Control-Request-Method`:

- پاسخ **204**
- کنترلر اجرا نمی‌شود
- هدرهای CORS ست می‌شوند

---

## Origin پویا و wildcard

```php
->allowOrigins(fn (string $origin, $request) => Tenant::hasOrigin($origin))
->allowOrigins(['*', 'https://example.com', '*.example.com'])
```

با `allowCredentials()` مقدار `Allow-Origin` برابر همان `Origin` درخواست می‌شود (نه `*`).

---

## استفاده دستی

```php
Cors::apply($response);
Cors::apply($response, 'api');
Cors::apply($response, $request, 'api');
```

---

## بهترین تمرین‌ها

1. در production از origin صریح استفاده کنید.
2. `cors:` را قبل از `auth` بگذارید تا preflight بدون لاگین موفق شود.
3. فقط هدرهای لازم را expose کنید.
4. `maxAge` مناسب برای کاهش preflight تنظیم کنید.
5. با [Rate Limiter](./rate-limiter.md) روی APIهای عمومی ترکیب کنید.

---

## مستندات مرتبط

- [فلو — Flow](../basic/flows.md)
- [روتر](../basic/routers.md)
- [Rate Limiter](./rate-limiter.md)

---

[← بازگشت به فهرست](../README.md)
