# HTTP-Request

[← Zurück zur Übersicht](../README.md)

Die Klasse `Pinoox\Component\Http\Request` verarbeitet HTTP-Eingaben: Query-String, Formular-POST, JSON-Body, Routenparameter und Datei-Uploads. In Controllern und Flows ist `Request` über **Dependency Injection** in Methodenparametern verfügbar.

> Es gibt keinen globalen **`request()`**-Helfer. Injizieren Sie `Request` oder verwenden Sie `$this->getRequest()` in Controllern.

---

## Zugriff in einem Controller

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` gibt zusammengeführte Daten aus Attributen, POST, Query, JSON und Dateien zurück.

---

## Aus einer bestimmten Quelle lesen

| Quelle | Methode | Beispiel |
|--------|--------|---------|
| Query-String | `queryOne()` | `$request->queryOne('page', 1)` |
| Formular-POST | `requestOne()` | `$request->requestOne('email')` |
| JSON-Body | `jsonOne()` | `$request->jsonOne('items')` |
| Routenparameter | `parametersOne()` | `$request->parametersOne('id')` |
| Alle Eingaben | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST-Feld: email
$email = $request->requestOne('email');

// Route: /product/{id}
$id = $request->parametersOne('id');
```

---

## Validierung

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

Oder holen Sie sich eine Validator-Instanz:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

Der Basis-Controller bietet außerdem **`$this->validate()`** und **`$this->validation()`**.

---

## Datei-Upload

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## Anfragetyp erkennen

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## Aktuelle Route und Collection

```php
$route = $request->route();
$collection = $request->collection();
```

---

## Vollständiges API-Controller-Beispiel

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

## Richtlinien

- Validieren Sie Benutzereingaben immer
- Lesen Sie JSON in APIs mit `jsonOne()` oder `get()`
- `Request` kann auch in Flows injiziert werden

---

## Verwandte Dokumente

- [Controller](./controllers.md)
- [HTTP-Response](./responses.md)
- [Validierung](./validation.md)
- [Router](./routers.md)

---

[← Zurück zur Übersicht](../README.md)
