# Router

[← Retour à l'index](../README.md)

Le routage Pinoox 3.x comporte deux couches : **Named Actions** (handlers logiques) et **Routes** (chemins URL et méthodes HTTP). Chaque app définit ses routes dans le dossier **`routes/`** et les enregistre dans **`app.php`**.

> N'utilisez pas **`Pinoox\Portal\Router::get`**. Importez les fonctions router depuis l'espace de noms **`Pinoox\Router`**.

---

## Enregistrer les fichiers de routes dans app.php

```php
'router' => [
    'routes' => [
        'routes/web.php',
        'routes/actions.php',
        'routes/api.php',
    ],
],
```

---

## Importer les fonctions router

```php
use function Pinoox\Router\{
    get, post, put, patch, delete,
    action, group, routes, collect, route
};
```

La fonction **`collection()`** n'existe pas dans pincore 3.x.

---

## Named Actions

Définissez un handler une fois dans `routes/actions.php` :

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## Mapper les URL dans web.php

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — référence à une action enregistrée
- `{id}` — paramètre dynamique (passé au contrôleur ou via `$request->parametersOne('id')`)

---

## Méthodes HTTP

```php
use function Pinoox\Router\{get, post, put, patch, delete};

post('login', [AuthController::class, 'login'])->name('login');
put('product/{id}', [ProductController::class, 'update'])->name('product.update');
delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
```

---

## Groupes de routes

Utilisez `group()` pour un préfixe partagé et un Flow sur plusieurs routes :

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

Chemins résultants : `/admin/dashboard`, `/admin/orders`

---

## Flow sur une route unique

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

L'alias correspondant doit être enregistré dans `app.php` sous `'alias'`.

---

## Fichier de routes API — `routes()` et `collect()`

```php
<?php
// routes/api.php

use App\com_acme_shop\Controller\ProductApiController;
use function Pinoox\Router\{collect, get, post, routes};

return routes([
    'version' => 'v1',
    'prefix' => '',
    'routes' => collect(function () {
        get('/products', [ProductApiController::class, 'index'])->name('products.index');
        post('/products', [ProductApiController::class, 'store'])->name('products.store');
        get('/products/{id}', [ProductApiController::class, 'show'])->name('products.show');
    }),
]);
```

`collect()` rassemble les routes dans un manifeste API. Renvoyez le manifeste final avec **`routes([...])`**.

---

## URL depuis le nom de route

```php
use function Pinoox\Router\route;

echo route('home');                    // URL de la route active
echo route('product.show', ['id' => 5]);
```

---

## Fallback (404)

```php
use Pinoox\Portal\View;
use function Pinoox\Router\get;

get('*', fn () => View::render('errors/404'))->name('fallback');
```

---

## Sélection de l'app active (niveau projet)

L'app qui traite une requête est configurée dans `config/app-router.config.php` (préfixe URL) ou `config/domain.config.php` (domaine) :

```php
// config/app-router.config.php
return [
    '/' => 'com_pinoox_welcome',
    '/shop' => 'com_acme_shop',
];
```

CLI :

```bash
php pinoox app:router set /shop com_acme_shop
```

---

## Documentation associée

- [Flow](./flows.md)
- [Contrôleurs](./controllers.md)
- [Request](./requests.md)
- [Votre première app](../start/your-first-app.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)
