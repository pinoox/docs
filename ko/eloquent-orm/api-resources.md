# API Resources

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x는 **`Pinoox\Component\Http\Api\ApiResource`**(Laravel JsonResource 아님)로 JSON API output을 구성합니다. output은 표준 `{ success, data, message, meta }` envelope으로 감싸집니다.

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
// array of toArray() for each item
```

---

## PayloadResource (custom array)

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## Response envelope

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

## pagination용 meta

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

## Tips

- Resource는 **API shape만** 정의 — query는 Model/Controller에.
- Resource에서 민감 field(`password`) 노출 금지.
- API Controller는 `ApiController`를 확장하고 `ok()` / `fail()` / `resource()` 사용.

---

## 관련 문서

- [Responses](../basic/responses.md)
- [Pagination](../database/pagination.md)
- [Serialization](./serialization.md)

---

[← 색인으로 돌아가기](../README.md)
