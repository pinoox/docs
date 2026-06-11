# Pinoox Browser (HTML) 테스트

[← 색인으로 돌아가기](../README.md)

Twig와 HTML page에는 **`appGet()`과 `assertSee()`가 있는 Feature test** 사용 — real browser나 Dusk 불필요. HTTP 시뮬레이션 후 HTML content assert.

---

## Prerequisites

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Home page — title과 text

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

## Form — field 존재

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

## POST 후 redirect

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

## 404 page

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## Database와 결합

page가 DB data에 의존하면 먼저 record 생성(`inApp` 내부), page 열기:

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

## Test 실행

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Limitation

이 접근은 client-side JavaScript(Vue/Vite SPA)를 실행하지 않음. SPA는 API test(`appPostJson`)와 필요 시 frontend layer 별도 E2E test.

---

## 관련 문서

- [HTTP tests](./http-tests.md)
- [Database tests](./database.md)
- [Views](../basic/views.md)
- [Templates](../basic/templates.md)
- [Serialization tests](./serialization.md)

---

[← 색인으로 돌아가기](../README.md)
