# भाषा और अनुवाद

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x **`lang/{locale}/*.lang.php`** फ़ाइलों के माध्यम से i18n का समर्थन करता है। मानक तरीका: PHP में **`t('file.key')`** या **`Lang::get('file.key')`** और Twig में **`{{ t('file.key') }}`**।

---

## फ़ाइल संरचना

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

पूर्ण key: `welcome.title` → फ़ाइल `welcome` + key `title`।

---

## PHP में उपयोग

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// बहुवचन (Pluralization)
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Twig में उपयोग

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Locale बदलें

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

डिफ़ॉल्ट locale `app.php` → `'lang'` से आता है।

---

## नेस्टेड placeholders

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## जाँचें कि कोई key मौजूद है

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Controller उदाहरण

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

## Validation और Lang

Validation संदेश `lang/{locale}/validation.lang.php` में रखें (देखें [Validation](./validation.md))।

---

## सुझाव

- Keys को तार्किक रूप से समूहीकृत करें: `product.title`, `cart.checkout` — एक विशाल फ़ाइल नहीं।
- SPAs के लिए, `pinoox.twig` में `PINOOX.LANG` के माध्यम से locale उपलब्ध कराएँ।
- Controllers में हार्ड-कोडेड UI स्ट्रिंग्स से बचें।

---

## संबंधित दस्तावेज़

- [Twig Templates](./templates.md)
- [Portal](./portal.md)
- [Validation](./validation.md)
- [Helpers](../advanced/helpers.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
