# API Resources

[← 索引に戻る](../README.md)

Pinoox 3.x は **`Pinoox\Component\Http\Api\ApiResource`**（Laravel JsonResource ではない）で JSON API 出力を整形します。出力は標準 `{ success, data, message, meta }` エンベロープでラップされます。

---

## ApiResource

```php
<?php
namespace App\com_acme_blog\Resource;

use Pinoox\Component\Http\Api\ApiResource;

final class PostResource extends ApiResource
{
    public function toArray(): array
    {
        $post = $this->resource;

        return [
            'id' => $post->post_id,
            'title' => $post->title,
            'status' => $post->status,
            'author' => [
                'id' => $post->author?->user_id,
                'name' => $post->author?->full_name,
            ],
            'created_at' => $post->created_at?->toIso8601String(),
        ];
    }
}
```

---

## Controller

```php
use Pinoox\Component\Kernel\Controller\ApiController;
use App\com_acme_blog\Model\PostModel;
use App\com_acme_blog\Resource\PostResource;

class PostController extends ApiController
{
    public function show(int $id)
    {
        $post = PostModel::with('author')->find($id);

        if ($post === null) {
            return $this->fail('NOT_FOUND', 'post.not_found', status: 404);
        }

        return $this->resource(new PostResource($post), 'post.loaded');
    }

    public function index()
    {
        $posts = PostModel::with('author')->get();

        return $this->ok(
            PostResource::collection($posts, PostResource::class),
            'posts.loaded',
        );
    }
}
```

---

## collection

```php
PostResource::collection($items, PostResource::class);
// 各アイテムの toArray() 配列
```

---

## PayloadResource（カスタム配列）

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## レスポンスエンベロープ

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Hello",
    "status": "published"
  },
  "message": "post.loaded",
  "meta": {}
}
```

---

## Pagination 用 meta

```php
$paginator = PostModel::paginate(15);

return $this->ok(
    PostResource::collection($paginator->items(), PostResource::class),
    meta: [
        'current_page' => $paginator->currentPage(),
        'last_page' => $paginator->lastPage(),
        'total' => $paginator->total(),
    ],
);
```

---

## ヒント

- Resource は **API 形状のみ** を定義 — クエリは Model/Controller に属する。
- 機密フィールド（`password`）を Resource で公開しない。
- API Controller は `ApiController` を継承し `ok()` / `fail()` / `resource()` を使用。

---

## 関連ドキュメント

- [Response](../basic/responses.md)
- [Pagination](../database/pagination.md)
- [Serialization](./serialization.md)

---

[← 索引に戻る](../README.md)
