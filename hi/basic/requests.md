# HTTP Request

[← इंडेक्स पर वापस जाएँ](../README.md)

`Pinoox\Component\Http\Request` class HTTP input handle करती है: query string, form POST, JSON body, route parameters, और file uploads। Controllers और Flows में `Request` method parameters पर **dependency injection** के ज़रिए उपलब्ध है।

> Global **`request()`** helper नहीं है। `Request` inject करें या controllers में `$this->getRequest()` उपयोग करें।

---

## Controller में access

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` attributes, POST, query, JSON, और files से merged data return करता है।

---

## Specific source से read करें

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

या Validator instance लें:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

Base controller **`$this->validate()`** और **`$this->validation()`** भी provide करता है।

---

## File upload

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## Request type detect करें

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## Current route और collection

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

- User input हमेशा validate करें
- APIs के लिए JSON `jsonOne()` या `get()` से read करें
- `Request` Flows में भी inject हो सकता है

---

## संबंधित docs

- [Controllers](./controllers.md)
- [HTTP Response](./responses.md)
- [Validation](./validation.md)
- [Router](./routers.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
