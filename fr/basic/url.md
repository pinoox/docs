# URL et construction de liens

[← Retour à l'index](../README.md)

Dans Pinoox 3.x, utilisez **`url()`** pour construire des URL internes. Ce helper utilise **`Url::link()`** et tient compte du domaine, du chemin d'installation (sous-dossier) et du segment de l'app courante.

> N'utilisez pas **`Url::get()`** ou **`Url::app()`**. Utilisez plutôt **`url()`**, **`Url::link()`** et **`Url::forApp()`**.

---

## PHP — helper `url()`

```php
// Lien relatif dans l'app active
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// Accesseur sans arguments
$accessor = url();
echo $accessor->app;               // URL de base de l'app
echo $accessor->site;              // origine + chemin du projet
echo $accessor->api;               // préfixe API

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // identique à url('products')
echo Url::forApp('com_acme_shop'); // URL de base d'une app spécifique
echo Url::current();               // URL de la page courante
echo Url::origin();                // https://example.com/pinoox
```

Préfixez `^` ou `~` pour les liens hors de la base de l'app :

```php
echo url('^about');                // depuis la racine du projet
echo Url::link('^config/app.php');
```

---

## Twig — accesseur `url()`

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

| Méthode accesseur | Rôle |
|-----------------|---------|
| `url().site` | origine + chemin du projet |
| `url().app` | origine + segment de l'app |
| `url().api` | préfixe API (par défaut `api/v1/`) |
| `url().resource('resources/logo.png')` | fichier statique sous `apps/{package}/` |
| `url('profile')` | lien de route dans l'app |

---

## Nom de route — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## Assets du thème — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL du fichier dans le thème actif
```

---

## Exemple de menu dans un contrôleur

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'Home', 'href' => url('/')],
    ['label' => 'Products', 'href' => url('products')],
    ['label' => 'Panel', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## Informations sur la requête

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## Conseils

- Ne codez pas les liens en dur ; utilisez toujours `url()` ou `Url::link()`
- Les fichiers dans `apps/{package}/resources/` utilisent `url().resource()` ou `asset()` ; les fichiers du thème utilisent **`assets()`**
- L'URL de base n'est pas définie manuellement dans la config ; elle est détectée depuis la requête HTTP

---

## Documentation associée

- [Chemin de fichier](./path.md)
- [Views](./views.md)
- [Modèles Twig](./templates.md)
- [Router](./routers.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)
