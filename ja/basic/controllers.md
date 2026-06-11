# Controller

[← 索引に戻る](../README.md)

Controller は HTTP リクエストを受け取り、必要に応じて Model と連携し、View または JSON レスポンスを返します。Pinoox 3.x では、アプリ Controller は `apps/{package}/Controller/` に、名前空間 `App\{package}\Controller` で配置します。

---

## Controller を作成

```bash
php pinoox controller:create HomeController com_acme_shop
```

ファイル: `apps/com_acme_shop/Controller/HomeController.php`

---

## 基本構造（HTML ページ）

```php
<?php

namespace App\com_acme_shop\Controller;

use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class HomeController extends Controller
{
    public function index()
    {
        return View::render('pages/home', [
            'title' => 'Home',
        ]);
    }
}
```

HTML をレンダリングする標準的な方法は **`View::render()`** です。`view()` ヘルパーも存在しますが、Controller では Portal を優先してください。

---

## API Controller

JSON エンドポイントには **`ApiController`** を継承し、**`ok()`**、**`fail()`**、**`validated()`** を使用します。

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
        $data = $this->validate([
            'title' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
        ]);

        $product = ProductModel::create($data);

        return $this->ok($product, 'Product saved.', status: 201);
    }

    public function destroy(Request $request, int $id)
    {
        $product = ProductModel::find($id);

        if (!$product) {
            return $this->fail('NOT_FOUND', 'Product not found.', status: 404);
        }

        $product->delete();

        return $this->ok(null, 'Deleted.');
    }
}
```

`$this->validate()` は `$this->getRequest()->validate()` と同じで、失敗時に `ValidationException` をスローします。

---

## ルートに接続

**routes/actions.php:**

```php
use App\com_acme_shop\Controller\HomeController;
use function Pinoox\Router\action;

action('home', [HomeController::class, 'index']);
```

**routes/web.php:**

```php
use function Pinoox\Router\get;

get('/', '@home')->name('home');
```

または Controller を直接バインド:

```php
get('about', [HomeController::class, 'about'])->name('about');
```

---

## Request の注入

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    // ...
}
```

Pinoox は Controller メソッドパラメータに `Request` を自動注入します。グローバル **`request()`** ヘルパーはありません — 注入、`$this->getRequest()`、または `$this->validate()` を使用してください。

---

## JSON レスポンス（代替手段）

```php
// response ヘルパー
return response()->json(['items' => $items], 200);

// 基底 Controller の protected メソッド
return $this->json(['items' => $items], 200);
```

構造化 API には **`ApiController`** と `$this->ok()` / `$this->fail()` を推奨します。

---

## リダイレクト

```php
return redirect(url('login'));
return redirect(url('panel/dashboard'));
```

**`redirect()`** ヘルパーは相対パスを **`Url::link()`** 経由で完全な URL に変換します。

---

## Model を使った完全な例

```php
<?php

namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Model\ProductModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class ProductController extends Controller
{
    public function show(Request $request, int $id)
    {
        $product = ProductModel::findOrFail($id);

        return View::render('pages/product', ['product' => $product]);
    }
}
```

---

## ガイドライン

- フォルダは **`Controller/`**（単数形）— `Controllers/` ではない
- 名前空間にパッケージ名を含める: `App\com_acme_shop\Controller`
- Controller は薄く保ち、重いロジックは `Component/` に置く
- `vendor/pinoox/pincore/` にアプリロジックを書かない

---

## 関連ドキュメント

- [Router](./routers.md)
- [Request](./requests.md)
- [Flow](./flows.md)
- [View](./views.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)
