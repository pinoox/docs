# HTTP Response

[← 索引に戻る](../README.md)

Pinoox 3.x では、すべての Controller が HTTP レスポンスを返す必要があります。HTML には **`View::render()`** を、JSON には **`response()->json()`** または **`ApiController`** を使用します。

---

## HTML レスポンス（標準）

```php
use Pinoox\Portal\View;

return View::render('pages/about', [
    'title' => 'About us',
]);
```

Twig なしの生 HTML:

```php
return response('<h1>About us</h1>', 200, [
    'Content-Type' => 'text/html; charset=UTF-8',
]);
```

---

## JSON レスポンス（API）

```php
public function list()
{
    $products = ProductModel::limit(10)->get();

    return response()->json([
        'success' => true,
        'data' => $products,
    ], 200);
}
```

`json()` パラメータ:

| パラメータ | 説明 |
|-----------|-------------|
| `$data` | JSON にシリアライズ可能な配列またはオブジェクト |
| `$status` | HTTP ステータスコード（デフォルト 200） |
| `$headers` | 追加ヘッダー（任意） |

---

## ApiController — 標準エンベロープ

```php
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function show(int $id)
    {
        $product = ProductModel::find($id);

        if (!$product) {
            return $this->fail('NOT_FOUND', 'Product not found.', status: 404);
        }

        return $this->ok($product);
    }
}
```

---

## 基底 Controller の `json()`

```php
return $this->json(['items' => $items], 200);
```

---

## リダイレクト

```php
return redirect(url('panel/dashboard'));
return redirect(url('login'));
```

---

## `View::response()` と `View::jsResponse()`

```php
use Pinoox\Portal\View;

// Response でラップされた HTML
return View::response('pages/home', ['title' => 'Home']);

// JavaScript を出力する Twig ファイル（例: pinoox.twig）
return View::jsResponse('pinoox.twig');
```

---

## API での Validation 例

```php
use Pinoox\Component\Http\Request;
use Pinoox\Component\Validation\ValidationException;
use Pinoox\Portal\Validation;

public function store(Request $request)
{
    try {
        $validated = Validation::validate($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        $product = ProductModel::create($validated);

        return response()->json([
            'success' => true,
            'data' => $product,
        ], 201);
    } catch (ValidationException $e) {
        return response()->json([
            'success' => false,
            'errors' => $e->errors(),
        ], 422);
    }
}
```

---

## ヒント

- API では `json()` が `Content-Type` を自動設定します
- HTTP ステータスコードを明示的に設定: 作成は `201`、Validation エラーは `422`、見つからない場合は `404`
- HTML ページは **`View::render()`** でレンダリング

---

## 関連ドキュメント

- [Request](./requests.md)
- [Controller](./controllers.md)
- [Validation](./validation.md)
- [View](./views.md)
- [Portal](./portal.md)

---

[← 索引に戻る](../README.md)
