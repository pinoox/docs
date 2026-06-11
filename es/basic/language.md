# Idioma y traducción

[← Volver al índice](../README.md)

Pinoox 3.x soporta i18n mediante archivos **`lang/{locale}/*.lang.php`**. El enfoque estándar: **`t('file.key')`** o **`Lang::get('file.key')`** en PHP y **`{{ t('file.key') }}`** en Twig.

---

## Estructura de archivos

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

Clave completa: `welcome.title` → archivo `welcome` + clave `title`.

---

## Uso en PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralización
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Uso en Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Cambiar locale

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

El locale por defecto viene de `app.php` → `'lang'`.

---

## Marcadores anidados

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## Comprobar si existe una clave

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Ejemplo en controller

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

## Validación y Lang

Pon los mensajes de validación en `lang/{locale}/validation.lang.php` (consulta [Validación](./validation.md)).

---

## Consejos

- Agrupa claves de forma lógica: `product.title`, `cart.checkout` — no un archivo gigante.
- Para SPAs, expón el locale en `pinoox.twig` vía `PINOOX.LANG`.
- Evita cadenas de UI hardcodeadas en controllers.

---

## Documentación relacionada

- [Plantillas Twig](./templates.md)
- [Portal](./portal.md)
- [Validación](./validation.md)
- [Helpers](../advanced/helpers.md)

---

[← Volver al índice](../README.md)
