# HTTP-тестирование в Pinoox

[← Вернуться к оглавлению](../README.md)

Для тестирования контроллеров, API и Flows используйте HTTP-хелперы Pinoox: `appGet()`, `appPost()` и `appPostJson()`. Каждый возвращает `TestResponse` со встроенными assertions.

---

## Предварительные условия

В `apps/{package}/tests/Pest.php` задайте пакет приложения в `beforeEach`:

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — список продуктов

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

## Form POST — отправка данных

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

## JSON POST — API

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

## Пользовательский запрос

Для заголовков, cookies или нестандартных HTTP-методов используйте `appCall()`:

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## Assertions TestResponse

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

## Тестирование Flow (например, auth)

Тестируйте маршруты, защищённые Flow, как любой другой endpoint:

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // Войдите пользователем внутри inApp, затем:
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## Запуск тестов

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## Связанные документы

- [Начало работы с тестированием](./getting-started.md)
- [Тесты JSON-сериализации](./serialization.md)
- [Тесты базы данных](./database.md)
- [Routers](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Responses](../basic/responses.md)
- [Requests](../basic/requests.md)

---

[← Вернуться к оглавлению](../README.md)
