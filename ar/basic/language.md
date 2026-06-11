# اللغة والترجمة

[← العودة إلى الفهرس](../README.md)

يدعم Pinoox 3.x التدويل (i18n) عبر ملفات **`lang/{locale}/*.lang.php`**. الأسلوب المعياري: **`t('file.key')`** أو **`Lang::get('file.key')`** في PHP و**`{{ t('file.key') }}`** في Twig.

---

## بنية الملفات

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

المفتاح الكامل: `welcome.title` → الملف `welcome` + المفتاح `title`.

---

## الاستخدام في PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralization
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## الاستخدام في Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## تغيير اللغة

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

اللغة الافتراضية تأتي من `app.php` → `'lang'`.

---

## عناصر نائبة متداخلة

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## التحقق من وجود مفتاح

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## مثال في متحكم

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

## التحقق واللغة

ضع رسائل التحقق في `lang/{locale}/validation.lang.php` (راجع [التحقق (Validation)](./validation.md)).

---

## نصائح

- جمّع المفاتيح منطقيًا: `product.title`، `cart.checkout` — وليس ملفًا ضخمًا واحدًا.
- لـ SPAs، اعرض اللغة في `pinoox.twig` عبر `PINOOX.LANG`.
- تجنّب النصوص الثابتة في المتحكمات.

---

## وثائق ذات صلة

- [قوالب Twig](./templates.md)
- [Portal](./portal.md)
- [التحقق (Validation)](./validation.md)
- [المساعدات (Helpers)](../advanced/helpers.md)

---

[← العودة إلى الفهرس](../README.md)
