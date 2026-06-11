# Pinoox'ta tarayıcı (HTML) testleri

[← Dizine dön](../README.md)

Twig ve HTML sayfaları için Pinoox **`appGet()` ve `assertSee()`** ile Feature testleri kullanır — gerçek tarayıcı veya Dusk gerekmez. HTTP simüle edilir ve HTML içeriği doğrulanır.

---

## Ön koşullar

```php
// apps/com_my_shop/tests/Pest.php
beforeEach(function () {
    appPackage('com_my_shop');
});
```

---

## Ana sayfa — başlık ve metin

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

## Form — alan varlığı

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

## POST sonrası yönlendirme

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

## 404 sayfası

```php
it('returns 404 for unknown page', function () {
    $response = appGet(appPackage(), '/this-page-does-not-exist');

    $response->assertStatus(404);
});
```

---

## Veritabanı ile birlikte

Sayfa DB verisine bağlıysa önce kayıt oluşturun (`inApp` içinde), ardından sayfayı açın:

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

## Testleri çalıştırma

```bash
php pinoox test com_my_shop --feature
php pinoox test com_my_shop -f HomePage
```

---

## Sınırlama

Bu yaklaşım istemci tarafı JavaScript'i (Vue/Vite SPA) çalıştırmaz. SPA'lar için API testleri (`appPostJson`) ve gerektiğinde frontend katmanında ayrı E2E testleri kullanın.

---

## İlgili dokümantasyon

- [HTTP testleri](./http-tests.md)
- [Veritabanı testleri](./database.md)
- [View](../basic/views.md)
- [Şablonlar](../basic/templates.md)
- [Serileştirme testleri](./serialization.md)

---

[← Dizine dön](../README.md)
