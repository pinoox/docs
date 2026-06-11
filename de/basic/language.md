# Sprache und Übersetzung

[← Zurück zum Index](../README.md)

Pinoox 3.x unterstützt i18n über Dateien **`lang/{locale}/*.lang.php`**. Der Standardansatz: **`t('file.key')`** oder **`Lang::get('file.key')`** in PHP und **`{{ t('file.key') }}`** in Twig.

---

## Dateistruktur

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

Vollständiger Schlüssel: `welcome.title` → Datei `welcome` + Schlüssel `title`.

---

## Verwendung in PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralisierung
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Verwendung in Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Locale ändern

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

Die Standard-Locale kommt aus `app.php` → `'lang'`.

---

## Verschachtelte Platzhalter

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## Prüfen, ob ein Schlüssel existiert

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Controller-Beispiel

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

## Validierung und Lang

Validierungsmeldungen in `lang/{locale}/validation.lang.php` ablegen (siehe [Validierung](./validation.md)).

---

## Tipps

- Schlüssel logisch gruppieren: `product.title`, `cart.checkout` — keine riesige Datei.
- Für SPAs Locale in `pinoox.twig` über `PINOOX.LANG` bereitstellen.
- Keine fest codierten UI-Strings in Controllern.

---

## Verwandte Dokumentation

- [Twig-Templates](./templates.md)
- [Portal](./portal.md)
- [Validierung](./validation.md)
- [Hilfsfunktionen](../advanced/helpers.md)

---

[← Zurück zum Index](../README.md)
