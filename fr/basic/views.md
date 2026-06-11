# Views

[← Retour à l'index](../README.md)

Dans Pinoox 3.x, les pages HTML sont rendues avec **Twig** dans le dossier du thème. Approche standard dans les contrôleurs : **`View::render()`** depuis le Portal.

---

## Structure du thème

```
apps/com_acme_shop/
├── app.php                 # 'theme' => 'default'
└── theme/default/
    ├── main.twig
    ├── layout.twig
    └── pages/
        └── home.twig
```

---

## Rendu dans un contrôleur (standard)

```php
use Pinoox\Portal\View;

public function index()
{
    return View::render('pages/home', [
        'title' => 'Shop',
        'products' => ProductModel::latest()->take(6)->get(),
    ]);
}
```

N'incluez pas l'extension `.twig` ; View résout le fichier automatiquement.

Le helper **`view()`** existe aussi et renvoie `View::ready()`, mais préférez **`View::render()`** dans les contrôleurs :

```php
// Équivalent helper — surtout pour set/exists sur le moteur
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // rare
```

---

## Sortie chaîne (sans Response)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// email, PDF, …
```

Le helper **`render()`** appelle directement `View::render()`.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

Renvoie le contenu Twig dans une `Response` HTTP.

---

## Données globales pour toutes les vues

```php
View::set('siteName', config('app.name'));
// ou
view()->set('siteName', config('app.name'));
```

Dans Twig :

```twig
<title>{{ siteName }} — {{ title }}</title>
```

---

## SEO (Pinoox 3.x)

```php
View::shareSeo([
    'title' => 'Products',
    'description' => 'Shop product list',
    'canonical' => url('products'),
    'image' => assets('dist/og-cover.jpg'),
]);

return View::render('pages/products');
```

Dans `partials/head.twig` :

```twig
{{ seo_tags()|raw }}
```

---

## SPA — shell avec Vite

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

Voir [Modèles Twig](./templates.md) pour les détails.

---

## Vérifier si une vue existe

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Conseils

- Gardez la logique métier dans Controller/Component ; Twig est réservé à la présentation
- Le thème actif provient de `app.php` → `'theme'`
- Pour du JSON pur, utilisez `response()->json()` ou `ApiController`

---

## Documentation associée

- [Modèles Twig](./templates.md)
- [URL et assets](./url.md)
- [Réponse HTTP](./responses.md)
- [Portal](./portal.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)
