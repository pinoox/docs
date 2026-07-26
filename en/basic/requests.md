# HTTP Request

[← Back to index](../README.md)

The `Pinoox\Component\Http\Request` class handles HTTP input: query string, form POST, JSON body, route parameters, and file uploads. In controllers and Flows, `Request` is available through **dependency injection** on method parameters.

> There is no global **`request()`** helper. Inject `Request` or use `$this->getRequest()` in controllers.

---

## Access in a controller

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` returns merged data from attributes, POST, query, JSON, and files.

---

## Read from a specific source

| Source | Method | Example |
|--------|--------|---------|
| Query string | `queryOne()` | `$request->queryOne('page', 1)` |
| Form POST | `requestOne()` | `$request->requestOne('email')` |
| JSON body | `jsonOne()` | `$request->jsonOne('items')` |
| Route parameter | `parametersOne()` | `$request->parametersOne('id')` |
| All inputs | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST field: email
$email = $request->requestOne('email');

// Route: /product/{id}
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

Or get a Validator instance:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

The base controller also provides **`$this->validate()`** and **`$this->validation()`**.

---

## File upload

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/apps/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## Detect request type

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## QUERY body

For routes registered with `query()` (HTTP `QUERY`), read the body the same way as POST/JSON:

```php
public function search(Request $request)
{
    $filters = $request->getPayload()->all();
    // or: $request->jsonOne('filters')
}
```

See [Router — QUERY method](./routers.md#query-method-rfc-10008).

---

## Current route and collection

```php
$route = $request->route();
$collection = $request->collection();
```

---

## Full API controller example

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

## Guidelines

- Always validate user input
- For APIs, read JSON with `jsonOne()` or `get()`
- `Request` can be injected into Flows as well

---

## Related docs

- [Controllers](./controllers.md)
- [HTTP Response](./responses.md)
- [Validation](./validation.md)
- [Router](./routers.md)

---

[← Back to index](../README.md)
