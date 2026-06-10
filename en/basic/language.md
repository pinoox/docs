# Language and Translation

[← Back to index](../../readme.md)

Pinoox 3.x supports i18n through **`lang/{locale}/*.lang.php`** files. The standard approach: **`t('file.key')`** or **`Lang::get('file.key')`** in PHP and **`{{ t('file.key') }}`** in Twig.

---

## File structure

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

Full key: `welcome.title` → file `welcome` + key `title`.

---

## Usage in PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralization
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Usage in Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Change locale

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

Default locale comes from `app.php` → `'lang'`.

---

## Nested placeholders

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## Check if a key exists

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Controller example

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

## Validation and Lang

Put validation messages in `lang/{locale}/validation.lang.php` (see [Validation](./validation.md)).

---

## Tips

- Group keys logically: `product.title`, `cart.checkout` — not one giant file.
- For SPAs, expose locale in `pinoox.twig` via `PINOOX.LANG`.
- Avoid hard-coded UI strings in controllers.

---

## Related docs

- [Twig Templates](./templates.md)
- [Portal](./portal.md)
- [Validation](./validation.md)
- [Helpers](../advanced/helpers.md)

---

[← Back to index](../../readme.md)
