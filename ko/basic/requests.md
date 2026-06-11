# HTTP Request

[← 색인으로 돌아가기](../README.md)

`Pinoox\Component\Http\Request` class는 HTTP input을 처리합니다: query string, form POST, JSON body, route parameter, file upload. Controller와 Flow에서는 method parameter **dependency injection**으로 `Request`를 사용할 수 있습니다.

> 전역 **`request()`** helper는 없습니다. `Request`를 inject하거나 Controller에서 `$this->getRequest()`를 사용하세요.

---

## Controller에서 접근

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)`는 attributes, POST, query, JSON, files에서 병합된 데이터를 반환합니다.

---

## 특정 소스에서 읽기

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

또는 Validator instance를 가져옵니다:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

base Controller는 **`$this->validate()`**와 **`$this->validation()`**도 제공합니다.

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

## Request 유형 감지

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## 현재 route와 collection

```php
$route = $request->route();
$collection = $request->collection();
```

---

## 전체 API Controller 예제

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

## 가이드라인

- 사용자 input은 항상 validate하세요
- API에서는 `jsonOne()` 또는 `get()`으로 JSON을 읽으세요
- Flow에도 `Request`를 inject할 수 있습니다

---

## 관련 문서

- [Controller](./controllers.md)
- [HTTP Response](./responses.md)
- [Validation](./validation.md)
- [Router](./routers.md)

---

[← 색인으로 돌아가기](../README.md)
