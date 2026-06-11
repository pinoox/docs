# Браузерное (HTML) тестирование в Pinoox

[← Вернуться к оглавлению](../README.md)

Для Twig и HTML-страниц Pinoox использует **Feature-тесты с `appGet()` и `assertSee()`** — реальный браузер или Dusk не требуются. HTTP симулируется, а HTML-содержимое проверяется assertions.

---

## Предварительные условия

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Главная страница — заголовок и текст

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

## Форма — наличие полей

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

## Redirect после POST

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

## Страница 404

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## В сочетании с базой данных

Если страница зависит от данных БД, сначала создайте записи (внутри `inApp`), затем откройте страницу:

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

## Запуск тестов

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Ограничение

Этот подход не выполняет клиентский JavaScript (Vue/Vite SPA). Для SPA используйте API-тесты (`appPostJson`) и при необходимости отдельные E2E-тесты на уровне фронтенда.

---

## Связанные документы

- [HTTP-тесты](./http-tests.md)
- [Тесты базы данных](./database.md)
- [Views](../basic/views.md)
- [Шаблоны](../basic/templates.md)
- [Тесты сериализации](./serialization.md)

---

[← Вернуться к оглавлению](../README.md)
