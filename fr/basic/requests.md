# Requête HTTP

[← Retour à l'index](../README.md)

La classe `Pinoox\Component\Http\Request` gère les entrées HTTP : query string, formulaire POST, corps JSON, paramètres de route et uploads de fichiers. Dans les contrôleurs et les Flows, `Request` est disponible via **l'injection de dépendances** sur les paramètres de méthode.

> Il n'existe pas de helper global **`request()`**. Injectez `Request` ou utilisez `$this->getRequest()` dans les contrôleurs.

---

## Accès dans un contrôleur

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` renvoie les données fusionnées des attributs, POST, query, JSON et fichiers.

---

## Lire depuis une source spécifique

| Source | Méthode | Exemple |
|--------|--------|---------|
| Query string | `queryOne()` | `$request->queryOne('page', 1)` |
| Formulaire POST | `requestOne()` | `$request->requestOne('email')` |
| Corps JSON | `jsonOne()` | `$request->jsonOne('items')` |
| Paramètre de route | `parametersOne()` | `$request->parametersOne('id')` |
| Toutes les entrées | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// Champ POST : email
$email = $request->requestOne('email');

// Route : /product/{id}
$id = $request->parametersOne('id');
```

---

## Validation

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

Ou obtenez une instance Validator :

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

Le contrôleur de base fournit aussi **`$this->validate()`** et **`$this->validation()`**.

---

## Upload de fichier

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## Détecter le type de requête

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## Route et collection courantes

```php
$route = $request->route();
$collection = $request->collection();
```

---

## Exemple complet de contrôleur API

```php
<?php

namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Model\ProductModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:200',
            'price' => 'required|numeric|min:0',
        ]);

        $product = ProductModel::create($data);

        return $this->ok($product, status: 201);
    }
}
```

---

## Recommandations

- Validez toujours les entrées utilisateur
- Pour les API, lisez le JSON avec `jsonOne()` ou `get()`
- `Request` peut aussi être injecté dans les Flows

---

## Documentation associée

- [Contrôleurs](./controllers.md)
- [Réponse HTTP](./responses.md)
- [Validation](./validation.md)
- [Router](./routers.md)

---

[← Retour à l'index](../README.md)
