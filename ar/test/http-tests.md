# اختبارات HTTP في Pinoox

[← العودة إلى الفهرس](../README.md)

لاختبار المتحكمات وAPIs وFlows، استخدم مساعدات HTTP في Pinoox: `appGet()` و`appPost()` و`appPostJson()`. كل واحدة تُرجع `TestResponse` مع assertions مدمجة.

---

## المتطلبات المسبقة

في `apps/{package}/tests/Pest.php`، اضبط حزمة التطبيق في `beforeEach`:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — قائمة المنتجات

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

## POST من نموذج — إرسال بيانات

```php
it('submits contact form', function () {
    $response = appPost(appPackage(), '/contact', [
        'name' => 'Ali',
        'email' => 'ali@example.com',
        'message' => 'Hello',
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

## طلب مخصص

للرؤوس أو cookies أو طرق HTTP غير شائعة، استخدم `appCall()`:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## assertions على TestResponse

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // HTML text
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // JSON array
$response->status();
$response->content();
```

---

## اختبار Flow (مثل auth)

اختبر مسارات محمية بـ Flow كأي نقطة نهاية أخرى:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // Log in the user inside inApp, then:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## تشغيل الاختبارات

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## وثائق ذات صلة

- [البدء مع الاختبار](./getting-started.md)
- [اختبارات تسلسل JSON](./serialization.md)
- [اختبارات قاعدة البيانات](./database.md)
- [المُوجّه (Routers)](../basic/routers.md)
- [المتحكمات (Controllers)](../basic/controllers.md)
- [الاستجابات (Responses)](../basic/responses.md)
- [الطلبات (Requests)](../basic/requests.md)

---

[← العودة إلى الفهرس](../README.md)
