# Pinoox 中的浏览器（HTML）测试

[← 返回索引](../README.md)

对于 Twig 和 HTML 页面，Pinoox 使用 **带 `appGet()` 和 `assertSee()` 的 Feature 测试** — 无需真实浏览器或 Dusk。HTTP 被模拟，并断言 HTML 内容。

---

## 前置条件

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## 首页 — 标题与文本

```php
// apps/com_my_shop/tests/Feature/HomePageTest.php

it('shows welcome message on home page', function () {
    $response = appGet(appPackage(), '/');

    $response
        ->assertOk()
        ->assertSee('My Shop');
});
```

---

## 表单 — 字段存在

```php
it('renders login form', function () {
    $response = appGet(appPackage(), '/login');

    $response
        ->assertOk()
        ->assertSee('name="email"')
        ->assertSee('name="password"');
});
```

---

## POST 后重定向

```php
it('redirects after successful login', function () {
    $response = appPost(appPackage(), '/login', [
        'email' => 'user@example.com',
        'password' => 'secret',
    ]);

    $response->assertStatus(302);
});
```

---

## 404 页面

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## 与数据库结合

若页面依赖数据库数据，先在 `inApp` 内创建记录，再打开页面：

```php
it('shows product name on detail page', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'PHP Book',
            'slug' => 'php-book',
        ]);
    });

    $response = appGet(appPackage(), '/products/php-book');

    $response->assertSee('PHP Book');
});
```

---

## 运行测试

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## 局限

此方式不执行客户端 JavaScript（Vue/Vite SPA）。SPA 请使用 API 测试（`appPostJson`），必要时在前端层单独做 E2E 测试。

---

## 相关文档

- [HTTP 测试](./http-tests.md)
- [数据库测试](./database.md)
- [视图](../basic/views.md)
- [模板](../basic/templates.md)
- [序列化测试](./serialization.md)

---

[← 返回索引](../README.md)
