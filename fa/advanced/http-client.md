# HTTP Client

[← بازگشت به فهرست](../README.md)

پینوکس 3.x برای درخواست‌های خروجی به APIهای خارجی از **`Pinoox\Portal\Http`** استفاده می‌کند (پشت‌صحنه: `Pinoox\Component\Http\Http` و Symfony HttpClient). این یک کتابخانه کمکی است — خود پینوکس API و روتر و اپ‌محوری خودش را دارد. همان الگویی که در اپ‌های سیستمی مثل `com_pinoox_manager` برای ارتباط با pinoox.com به کار رفته است.

> ترجیحاً از **`Pinoox\Portal\Http`** استفاده کنید. فراخوانی استاتیک روی `Pinoox\Component\Http\Http` برای سازگاری عقب‌رو همچنان کار می‌کند.

---

## import

```php
use Pinoox\Portal\Http;
use Symfony\Contracts\HttpClient\ResponseInterface;
```

---

## متدهای کوتاه

```php
$response = Http::get('https://api.example.com/v1/status');
$response = Http::post('https://api.example.com/v1/users', [
    'json' => ['name' => 'Ali'],
]);
$response = Http::put($url, $options);
$response = Http::patch($url, $options);
$response = Http::delete($url, $options);
$response = Http::query($url, [
    'json' => ['filters' => ['status' => 'active']],
]);
```

متدهای پشتیبانی‌شده: `GET`, `POST`, `PUT`, `PATCH`, `DELETE`, `QUERY`, `HEAD`, `OPTIONS`.

### QUERY (خروجی)

وقتی API بالادستی متد HTTP `QUERY` را می‌پذیرد (امن/idempotent با body)، از `Http::query()` استفاده کنید:

```php
$response = Http::query('https://api.example.com/v1/search', [
    'json' => [
        'q' => 'pinoox',
        'filters' => ['type' => 'app'],
    ],
    'timeout' => 15,
]);
```

---

## گزینه‌های درخواست

Symfony HttpClient گزینه‌های استاندارد را می‌پذیرد:

```php
$response = Http::post('https://api.example.com/oauth/token', [
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ],
    'json' => [
        'grant_type' => 'client_credentials',
    ],
    'timeout' => 15,
]);
```

| کلید | کاربرد |
|------|--------|
| `json` | بدنه JSON |
| `body` | بدنه خام |
| `headers` | هدرهای HTTP |
| `query` | Query string |
| `timeout` | زمان انتظار (ثانیه) |
| `auth_basic` | `[user, pass]` |

پیش‌فرض محدود (scoped):

```php
$api = Http::withOptions(['timeout' => 15, 'headers' => [
    'Authorization' => 'Bearer ' . $token,
]]);

$response = $api->get('https://api.example.com/v1/me');
```

---

## خواندن پاسخ

```php
use Pinoox\Component\Kernel\Controller\ApiController;

class StatusController extends ApiController
{
    private function decodeResponse(?ResponseInterface $response): array
    {
        if (!$response) {
            return [];
        }

        return json_decode($response->getContent(), true) ?? [];
    }

    public function index()
    {
        $response = Http::get('https://api.example.com/v1/items');
        $data = $this->decodeResponse($response);

        if ($response && $response->getStatusCode() === 200) {
            return $this->ok($data);
        }

        return $this->fail('UPSTREAM_ERROR', 'api.unavailable', status: 502);
    }
}
```

---

## مثال واقعی (manager)

```php
use Pinoox\Portal\Http;
use Pinoox\Portal\Url;

$response = Http::post('https://www.pinoox.com/api/manager/v1/account/getData', [
    'json' => [
        'remote_url' => Url::origin(),
        'token_key' => config('connect.token_key'),
    ],
]);
```

---

## مدیریت خطا

`Http::request()` در صورت `TransportExceptionInterface` مقدار `null` برمی‌گرداند. همیشه پاسخ را بررسی کنید:

```php
$response = Http::get($url);

if ($response === null) {
    return $this->fail('NETWORK_ERROR', 'network.unreachable', status: 503);
}
```

---

## نکات

- برای API داخلی همان اپ از Router و کنترلر استفاده کنید؛ Http Client فقط برای **درخواست خارجی** است.
- URL و کلید API را در `config/` یا `.env` اپ نگه دارید، نه hard-code در کنترلر.
- Guzzle به‌صورت مستقیم در pincore استفاده نمی‌شود؛ Symfony HttpClient جایگزین پیشنهادی است.
- مسیرهای ورودی با `QUERY`: [روتر](../basic/routers.md).

---

## مستندات مرتبط

- [روتر — Router](../basic/routers.md)
- [کنترلر — Controllers](../basic/controllers.md)
- [پاسخ — Responses](../basic/responses.md)
- [پیکربندی — Config](../basic/config.md)

---

[← بازگشت به فهرست](../README.md)
