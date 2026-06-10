# تست HTTP در پینوکس

برای تست کنترلر، API و Flow از helperهای HTTP پینوکس استفاده کنید: `appGet()`، `appPost()` و `appPostJson()`. هر کدام یک `TestResponse` برمی‌گردانند که assertionهای آماده دارد.

---

## پیش‌نیاز

در `apps/{package}/tests/Pest.php` پکیج اپ را در `beforeEach` تنظیم کنید:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — لیست محصولات

```php
// apps/com_my_shop/tests/Feature/ProductApiTest.php

it('returns product list', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $response
        ->assertOk()
        ->assertJsonPath('success', true);
});
```

---

## POST فرم — ارسال داده

```php
it('submits contact form', function () {
    $response = appPost(appPackage(), '/contact', [
        'name' => 'علی',
        'email' => 'ali@example.com',
        'message' => 'سلام',
    ]);

    $response->assertOk();
});
```

---

## POST JSON — API

```php
it('creates an order', function () {
    $response = appPostJson(appPackage(), '/api/v1/orders', [
        'product_id' => 1,
        'qty' => 2,
    ]);

    $response->assertStatus(201);
});
```

---

## درخواست سفارشی

برای header، cookie یا متد غیرمعمول از `appCall()` استفاده کنید:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'محصول جدید'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## Assertionهای TestResponse

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // متن HTML
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // آرایه JSON
$response->status();
$response->content();
```

---

## تست Flow (مثلاً auth)

روت‌های محافظت‌شده با Flow را مثل هر endpoint دیگر تست کنید:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // داخل inApp کاربر را لاگین کنید، سپس:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## اجرا

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## مستندات مرتبط

- [شروع تست در پینوکس](./getting-started.md)
- [تست سریال‌سازی JSON](./serialization.md)
- [تست دیتابیس](./database.md)
- [Routers](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Requests](../basic/requests.md)
