# Pagination

[← 索引に戻る](../README.md)

Pinoox 3.x は pincore 基底 Eloquent Model 経由で Illuminate **`paginate()`** をサポートします。API では **`meta`** フィールド付きの標準エンベロープで結果を返します。

---

## Model 上の paginate

```php
<?php
namespace App\com_acme_shop\Model;

use Pinoox\Component\Database\Model;

class PostModel extends Model
{
    protected $table = 'posts';
}
```

```php
use App\com_acme_shop\Model\PostModel;

$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->paginate(15);
```

`$posts` は `LengthAwarePaginator` です。

---

## Query Builder による paginate

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## paginate パラメータ

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

デフォルトクエリ文字列: `?page=2`

---

## simplePaginate と cursorPaginate

```php
PostModel::simplePaginate(15);      // 総件数なし — 高速
PostModel::cursorPaginate(15);      // 無限フィード向け
```

---

## meta 付き API レスポンス

```php
use Pinoox\Component\Kernel\Controller\ApiController;

class PostController extends ApiController
{
    public function index(Request $request)
    {
        $posts = PostModel::query()
            ->where('status', 'published')
            ->paginate((int) $request->get('per_page', 15));

        return $this->ok(
            data: $posts->items(),
            message: 'posts.loaded',
            meta: [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        );
    }
}
```

エンベロープ:

```json
{
  "success": true,
  "data": [...],
  "message": "posts.loaded",
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

---

## ApiResource との組み合わせ

```php
use App\com_acme_shop\Resource\PostResource;

$posts = PostModel::paginate(15);

return $this->ok(
    PostResource::collection($posts->items(), PostResource::class),
    meta: [
        'current_page' => $posts->currentPage(),
        'last_page' => $posts->lastPage(),
        'per_page' => $posts->perPage(),
        'total' => $posts->total(),
    ],
);
```

---

## フロントエンド（Vue）

```js
const { data, meta } = unwrapApiResponse(await postAPI.list({ page: 2, per_page: 15 }));
// meta.current_page, meta.last_page, ...
```

---

## ヒント

- クエリ文字列から `per_page` を読み取り上限を設ける（例: 100）
- 総件数不要の大きなリストには `simplePaginate` が適する
- フィルターは **`paginate()` の前** に適用

---

## 関連ドキュメント

- [Query Builder](./query-builder.md)
- [API Resources](../eloquent-orm/api-resources.md)
- [Response](../basic/responses.md)

---

[← 索引に戻る](../README.md)
