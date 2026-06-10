# تست مرورگر (HTML) در پینوکس

پینوکس برای صفحات Twig و HTML از **تست Feature با `appGet()` و `assertSee()`** استفاده می‌کند — بدون نیاز به مرورگر واقعی یا Dusk. درخواست HTTP شبیه‌سازی می‌شود و محتوای HTML assert می‌شود.

---

## پیش‌نیاز

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## صفحه اصلی — عنوان و متن

```php
// apps/com_my_shop/tests/Feature/HomePageTest.php

it('shows welcome message on home page', function () {
    $response = appGet(appPackage(), '/');

    $response
        ->assertOk()
        ->assertSee('فروشگاه من');
});
```

---

## فرم — وجود فیلدها

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

## ریدایرکت بعد از POST

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

## صفحه 404

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## ترکیب با دیتابیس

اگر صفحه به داده DB وابسته است، ابتدا رکورد بسازید (در `inApp`) سپس صفحه را باز کنید:

```php
it('shows product name on detail page', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'کتاب PHP',
            'slug' => 'php-book',
        ]);
    });

    $response = appGet(appPackage(), '/products/php-book');

    $response->assertSee('کتاب PHP');
});
```

---

## اجرا

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## محدودیت

این روش JavaScript سمت کلاینت (Vue/Vite SPA) را اجرا نمی‌کند. برای SPA از تست API (`appPostJson`) و در صورت نیاز تست E2E جداگانه در لایه فرانت استفاده کنید.

---

## مستندات مرتبط

- [تست HTTP](./http-tests.md)
- [تست دیتابیس](./database.md)
- [Viewها](../basic/views.md)
- [Templates](../basic/templates.md)
- [تست سریال‌سازی](./serialization.md)
