# Pinoox 中的 HTTP 测试

[← 返回索引](../README.md)

要测试控制器、API 和 Flow，使用 Pinoox HTTP 辅助函数：`appGet()`、`appPost()` 和 `appPostJson()`。每个都返回带内置断言的 `TestResponse`。

---

## 前置条件

在 `apps/{package}/tests/Pest.php` 的 `beforeEach` 中设置应用包：

```php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## GET — 产品列表

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

## 表单 POST — 提交数据

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

## 自定义请求

需要请求头、Cookie 或不常见 HTTP 方法时，使用 `appCall()`：

```php
$response = appCall(appPackage(), 'PUT', '/api/v1/products/5', [
    'json' => ['title' => 'New product'],
    'headers' => ['Authorization' => 'Bearer token'],
]);
```

---

## TestResponse 断言

```php
$response->assertOk();                    // 200
$response->assertStatus(404);
$response->assertSee('Hello');           // HTML 文本
$response->assertJsonPath('data.id', 5);
$response->json('data.items');           // JSON 数组
$response->status();
$response->content();
```

---

## 测试 Flow（例如认证）

像测试其他端点一样测试受 Flow 保护的路由：

```php
it('requires authentication', function () {
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertStatus(401);
});

it('allows authenticated user', function () {
    // 在 inApp 内登录用户，然后：
    $response = appGet(appPackage(), '/panel/dashboard');

    $response->assertOk();
});
```

---

## 运行测试

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f ProductApi
```

---

## 相关文档

- [测试入门](./getting-started.md)
- [JSON 序列化测试](./serialization.md)
- [数据库测试](./database.md)
- [路由](../basic/routers.md)
- [控制器](../basic/controllers.md)
- [响应](../basic/responses.md)
- [请求](../basic/requests.md)

---

[← 返回索引](../README.md)
