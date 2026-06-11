# Dil ve çeviri

[← Dizine dön](../README.md)

Pinoox 3.x, **`lang/{locale}/*.lang.php`** dosyaları üzerinden i18n destekler. Standart yaklaşım: PHP'de **`t('file.key')`** veya **`Lang::get('file.key')`**, Twig'de **`{{ t('file.key') }}`**.

---

## Dosya yapısı

```
apps/com_acme_shop/
├── app.php              # 'lang' => 'en'
└── lang/
    ├── fa/
    │   ├── welcome.lang.php
    │   └── product.lang.php
    └── en/
        └── welcome.lang.php
```

```php
// lang/en/welcome.lang.php
return [
    'title' => 'Welcome to the shop',
    'hello' => 'Hello :name!',
    'items' => 'One item|:count items',
];
```

Tam anahtar: `welcome.title` → dosya `welcome` + anahtar `title`.

---

## PHP'de kullanım

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralization
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Twig'de kullanım

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Yerel ayarı değiştirme

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

Varsayılan yerel ayar `app.php` → `'lang'`'den gelir.

---

## İç içe placeholder'lar

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## Anahtarın varlığını kontrol etme

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Controller örneği

```php
use Pinoox\Portal\View;

public function index()
{
    return View::render('pages/home', [
        'heading' => t('welcome.title'),
        'cta' => t('welcome.shop_now'),
    ]);
}
```

---

## Validasyon ve Lang

Validasyon mesajlarını `lang/{locale}/validation.lang.php` içine koyun (bkz. [Validasyon](./validation.md)).

---

## İpuçları

- Anahtarları mantıklı gruplayın: `product.title`, `cart.checkout` — tek dev dosya değil.
- SPA'lar için yerel ayarı `pinoox.twig` üzerinden `PINOOX.LANG` ile açığa çıkarın.
- Controller'larda sabit kodlanmış UI metinlerinden kaçının.

---

## İlgili dokümantasyon

- [Twig şablonları](./templates.md)
- [Portal](./portal.md)
- [Validasyon](./validation.md)
- [Helper'lar](../advanced/helpers.md)

---

[← Dizine dön](../README.md)
