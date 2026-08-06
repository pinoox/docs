# Request HTTP

[← Volver al índice](../README.md)

La clase `Pinoox\Component\Http\Request` maneja la entrada HTTP: query string, formularios POST, cuerpo JSON, parámetros de ruta y subida de archivos. En los controllers y Flows, `Request` está disponible mediante **inyección de dependencias** en los parámetros de los métodos.

> No hay un helper global **`request()`**. Inyecta `Request` o usa `$this->getRequest()` en los controllers.

---

## Acceso en un controller

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` devuelve los datos combinados de attributes, POST, query, JSON y archivos.

---

## Leer de una fuente específica

| Fuente | Método | Ejemplo |
|--------|--------|---------|
| Query string | `queryOne()` | `$request->queryOne('page', 1)` |
| Formulario POST | `requestOne()` | `$request->requestOne('email')` |
| Cuerpo JSON | `jsonOne()` | `$request->jsonOne('items')` |
| Parámetro de ruta | `parametersOne()` | `$request->parametersOne('id')` |
| Todas las entradas | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// campo POST: email
$email = $request->requestOne('email');

// Ruta: /product/{id}
$id = $request->parametersOne('id');
```

---

## Validación

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

O bien obtén una instancia del Validator:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

El controller base también proporciona **`$this->validate()`** y **`$this->validation()`**.

---

## Subida de archivos

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## Detectar el tipo de petición

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## Ruta actual y colección

```php
$route = $request->route();
$collection = $request->collection();
```

---

## Ejemplo completo de controller de API

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

## Pautas

- Valida siempre la entrada del usuario
- Para APIs, lee el JSON con `jsonOne()` o `get()`
- `Request` también puede inyectarse en los Flows

---

## Documentación relacionada

- [Controllers](./controllers.md)
- [Response HTTP](./responses.md)
- [Validación](./validation.md)
- [Router](./routers.md)

---

[← Volver al índice](../README.md)
