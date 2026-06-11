# Langue et traduction

[← Retour à l'index](../README.md)

Pinoox 3.x prend en charge l'i18n via les fichiers **`lang/{locale}/*.lang.php`**. Approche standard : **`t('file.key')`** ou **`Lang::get('file.key')`** en PHP et **`{{ t('file.key') }}`** dans Twig.

---

## Structure des fichiers

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

Clé complète : `welcome.title` → fichier `welcome` + clé `title`.

---

## Utilisation en PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralisation
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Utilisation dans Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Changer la locale

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

La locale par défaut provient de `app.php` → `'lang'`.

---

## Placeholders imbriqués

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## Vérifier si une clé existe

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Exemple dans un contrôleur

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

## Validation et Lang

Placez les messages de validation dans `lang/{locale}/validation.lang.php` (voir [Validation](./validation.md)).

---

## Conseils

- Regroupez les clés logiquement : `product.title`, `cart.checkout` — pas un seul fichier géant.
- Pour les SPA, exposez la locale dans `pinoox.twig` via `PINOOX.LANG`.
- Évitez les chaînes UI codées en dur dans les contrôleurs.

---

## Documentation associée

- [Modèles Twig](./templates.md)
- [Portal](./portal.md)
- [Validation](./validation.md)
- [Helpers](../advanced/helpers.md)

---

[← Retour à l'index](../README.md)
