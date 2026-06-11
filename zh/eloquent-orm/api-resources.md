# API 资源

[← 返回索引](../README.md)

Pinoox 3.x 使用 **`Pinoox\Component\Http\Api\ApiResource`** 来塑造 JSON API 输出（不是 Laravel JsonResource）。输出包裹在标准的 `{ success, data, message, meta }` 信封中。

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

## 控制器

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
// 每项的 toArray() 数组
```

---

## PayloadResource（自定义数组）

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## 响应信封

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

## 分页 meta

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

## 提示

- 资源只定义 **API 形状** — 查询属于 Model/Controller。
- 不要在资源中暴露敏感字段（`password`）。
- API 控制器应继承 `ApiController`，并使用 `ok()` / `fail()` / `resource()`。

---

## 相关文档

- [响应](../basic/responses.md)
- [分页](../database/pagination.md)
- [序列化](./serialization.md)

---

[← 返回索引](../README.md)
