# زبان و ترجمه (Lang)

پینوکس ۳.x از فایل‌های **`lang/{locale}/*.lang.php`** برای چندزبانه‌سازی پشتیبانی می‌کند. روش استاندارد: **`t('file.key')`** یا **`Lang::get('file.key')`** در PHP و **`{{ t('file.key') }}`** در Twig.

---

## ساختار فایل

```
apps/com_acme_shop/
├── app.php              # 'lang' => 'fa'
└── lang/
    ├── fa/
    │   ├── welcome.lang.php
    │   └── product.lang.php
    └── en/
        └── welcome.lang.php
```

```php
// lang/fa/welcome.lang.php
return [
    'title' => 'به فروشگاه خوش آمدید',
    'hello' => 'سلام :name!',
    'items' => 'یک مورد|:count مورد',
];
```

کلید کامل: `welcome.title` → فایل `welcome` + کلید `title`.

---

## استفاده در PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'علی']);
echo Lang::get('product.add_to_cart');

// جمع (Pluralization)
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## استفاده در Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## تغییر locale

```php
Lang::setLocale('en');
$current = Lang::getLocale();   // fa
```

locale پیش‌فرض از `app.php` → `'lang'` می‌آید.

---

## placeholder تو در تو

```php
// lang/fa/user.lang.php
// 'profile' => 'کاربر: :user.name'

t('user.profile', ['user' => ['name' => 'رضا']]);
```

---

## بررسی وجود کلید

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## مثال در کنترلر

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

## SEO و Lang

```php
View::shareSeo([
    'title' => Lang::get('welcome.seo_title'),
    'description' => Lang::get('welcome.seo_description'),
]);
```

---

## اعتبارسنجی و Lang

پیام‌های validation را در `lang/fa/validation.lang.php` قرار دهید (رجوع به [اعتبارسنجی](validation.md)).

---

## نکات

- کلیدها را گروه‌بندی کنید: `product.title`، `cart.checkout`
- برای SPA، locale را در `pinoox.twig` داخل `PINOOX.LANG` expose کنید
- از hard-code متن فارسی در Controller خودداری کنید

---

## مستندات مرتبط

- [قالب Twig](templates.md)
- [Portal](portal.md)
- [اعتبارسنجی](validation.md)
- [ساختار پروژه](../start/structure.md)
