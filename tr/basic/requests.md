# HTTP Request

[← Dizine dön](../README.md)

`Pinoox\Component\Http\Request` sınıfı HTTP girdisini işler: query string, form POST, JSON gövdesi, route parametreleri ve dosya yüklemeleri. Controller ve Flow'larda `Request`, metot parametrelerinde **bağımlılık enjeksiyonu** ile kullanılabilir.

> Global **`request()`** helper'ı yoktur. `Request` enjekte edin veya controller'larda `$this->getRequest()` kullanın.

---

## Controller'da erişim

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)`, attribute'lar, POST, query, JSON ve dosyalardan birleştirilmiş veri döndürür.

---

## Belirli kaynaktan okuma

| Kaynak | Metot | Örnek |
|--------|--------|---------|
| Sorgu dizesi | `queryOne()` | `$request->queryOne('page', 1)` |
| Form POST | `requestOne()` | `$request->requestOne('email')` |
| JSON gövdesi | `jsonOne()` | `$request->jsonOne('items')` |
| Route parametresi | `parametersOne()` | `$request->parametersOne('id')` |
| Tüm girdiler | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST field: email
$email = $request->requestOne('email');

// Route: /product/{id}
$id = $request->parametersOne('id');
```

---

## Validasyon

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

Veya Validator örneği alın:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

Temel controller ayrıca **`$this->validate()`** ve **`$this->validation()`** sağlar.

---

## Dosya yükleme

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/apps/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## İstek türünü algılama

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## Mevcut route ve collection

```php
$route = $request->route();
$collection = $request->collection();
```

---

## Tam API controller örneği

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

## Yönergeler

- Kullanıcı girdisini her zaman doğrulayın
- API'ler için JSON'u `jsonOne()` veya `get()` ile okuyun
- `Request` Flow'lara da enjekte edilebilir

---

## İlgili dokümantasyon

- [Controller](./controllers.md)
- [HTTP Response](./responses.md)
- [Validasyon](./validation.md)
- [Router](./routers.md)

---

[← Dizine dön](../README.md)
