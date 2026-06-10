# منابع API (ApiResource)

[← بازگشت به فهرست](../../readme-fa.md)

پینوکس 3.x برای شکل‌دهی JSON API از **`Pinoox\Component\Http\Api\ApiResource`** استفاده می‌کند (نه Laravel JsonResource). خروجی در envelope استاندارد `{ success, data, message, meta }` قرار می‌گیرد.

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

## کنترلر

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
// آرایه‌ای از toArray() هر آیتم
```

---

## PayloadResource (آرایه دلخواه)

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## envelope خروجی

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "سلام",
    "status": "published"
  },
  "message": "post.loaded",
  "meta": {}
}
```

---

## meta برای pagination

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

## نکات

- Resource فقط **شکل API** را تعریف می‌کند — query در Model/Controller.
- فیلدهای حساس (`password`) را در Resource expose نکنید.
- کنترلر API از `ApiController` ارث ببرد و `ok()` / `fail()` / `resource()` استفاده کند.

---

## مستندات مرتبط

- [پاسخ API — API Response](../../pinoox%20docs/pinoox-api-response.md)
- [صفحه‌بندی](../database/pagination.md)
- [سریال‌سازی](./serialization.md)

---

[← بازگشت به فهرست](../../readme-fa.md)
