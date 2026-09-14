# Requisição HTTP

[← Voltar ao índice](../README.md)

A classe `Pinoox\Component\Http\Request` trata a entrada HTTP: query string, POST de formulário, corpo JSON, parâmetros de rota e uploads de arquivo. Em controllers e Flows, `Request` está disponível por **injeção de dependência** nos parâmetros dos métodos.

> Não existe helper global **`request()`**. Injete `Request` ou use `$this->getRequest()` nos controllers.

---

## Acesso em um controller

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` retorna dados mesclados de attributes, POST, query, JSON e arquivos.

---

## Ler de uma fonte específica

| Fonte | Método | Exemplo |
|--------|--------|---------|
| Query string | `queryOne()` | `$request->queryOne('page', 1)` |
| POST de formulário | `requestOne()` | `$request->requestOne('email')` |
| Corpo JSON | `jsonOne()` | `$request->jsonOne('items')` |
| Parâmetro de rota | `parametersOne()` | `$request->parametersOne('id')` |
| Todas as entradas | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// Campo POST: email
$email = $request->requestOne('email');

// Rota: /product/{id}
$id = $request->parametersOne('id');
```

---

## Validação

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

Ou obtenha uma instância de Validator:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

O controller base também oferece **`$this->validate()`** e **`$this->validation()`**.

---

## Upload de arquivo

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## Detectar tipo de requisição

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## Rota e collection atuais

```php
$route = $request->route();
$collection = $request->collection();
```

---

## Exemplo completo de controller de API

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

## Diretrizes

- Sempre valide a entrada do usuário
- Para APIs, leia JSON com `jsonOne()` ou `get()`
- `Request` também pode ser injetado em Flows

---

## Documentação relacionada

- [Controllers](./controllers.md)
- [Resposta HTTP](./responses.md)
- [Validação](./validation.md)
- [Router](./routers.md)

---

[← Voltar ao índice](../README.md)
