# HTTP Request

[← 索引に戻る](../README.md)

`Pinoox\Component\Http\Request` クラスは HTTP 入力を処理します: クエリ文字列、フォーム POST、JSON ボディ、ルートパラメータ、ファイルアップロード。Controller と Flow では、メソッドパラメータへの **依存性注入** で `Request` が利用できます。

> グローバル **`request()`** ヘルパーはありません。Controller では `Request` を注入するか `$this->getRequest()` を使用してください。

---

## Controller でのアクセス

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` は attributes、POST、query、JSON、files からマージされたデータを返します。

---

## 特定のソースから読み取り

| ソース | メソッド | 例 |
|--------|--------|---------|
| クエリ文字列 | `queryOne()` | `$request->queryOne('page', 1)` |
| フォーム POST | `requestOne()` | `$request->requestOne('email')` |
| JSON ボディ | `jsonOne()` | `$request->jsonOne('items')` |
| ルートパラメータ | `parametersOne()` | `$request->parametersOne('id')` |
| すべての入力 | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST フィールド: email
$email = $request->requestOne('email');

// ルート: /product/{id}
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

または Validator インスタンスを取得:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

基底 Controller には **`$this->validate()`** と **`$this->validation()`** もあります。

---

## ファイルアップロード

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## リクエストタイプの検出

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## 現在のルートと collection

```php
$route = $request->route();
$collection = $request->collection();
```

---

## 完全な API Controller の例

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

## ガイドライン

- ユーザー入力は常に Validation する
- API では `jsonOne()` または `get()` で JSON を読み取る
- Flow にも `Request` を注入できる

---

## 関連ドキュメント

- [Controller](./controllers.md)
- [HTTP Response](./responses.md)
- [Validation](./validation.md)
- [Router](./routers.md)

---

[← 索引に戻る](../README.md)
