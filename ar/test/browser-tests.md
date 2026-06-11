# اختبار المتصفح (HTML) في Pinoox

[← العودة إلى الفهرس](../README.md)

لصفحات Twig وHTML، يستخدم Pinoox **اختبارات Feature مع `appGet()` و`assertSee()`** — لا يلزم متصفح حقيقي أو Dusk. يُحاكى HTTP ويُؤكَّد محتوى HTML.

---

## المتطلبات المسبقة

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## الصفحة الرئيسية — العنوان والنص

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

## النموذج — وجود الحقول

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

## إعادة التوجيه بعد POST

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

## صفحة 404

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## مدمج مع قاعدة البيانات

إذا اعتمدت الصفحة على بيانات DB، أنشئ السجلات أولًا (داخل `inApp`)، ثم افتح الصفحة:

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

## تشغيل الاختبارات

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## القيد

هذا الأسلوب لا ينفّذ JavaScript من جانب العميل (Vue/Vite SPA). لـ SPAs، استخدم اختبارات API (`appPostJson`) وعند الحاجة اختبارات E2E منفصلة في طبقة الواجهة الأمامية.

---

## وثائق ذات صلة

- [اختبارات HTTP](./http-tests.md)
- [اختبارات قاعدة البيانات](./database.md)
- [العروض (Views)](../basic/views.md)
- [القوالب (Templates)](../basic/templates.md)
- [اختبارات التسلسل](./serialization.md)

---

[← العودة إلى الفهرس](../README.md)
