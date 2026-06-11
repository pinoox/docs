# موارد API (API Resources)

[← العودة إلى الفهرس](../README.md)

يشكّل Pinoox 3.x مخرجات JSON للـ API عبر **`Pinoox\Component\Http\Api\ApiResource`** (وليس Laravel JsonResource). المخرجات ملفوفة في الغلاف المعياري `{ success, data, message, meta }`.

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

## المتحكم

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

## PayloadResource (مصفوفة مخصصة)

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## غلاف الاستجابة

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

## meta للتصفح

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

## نصائح

- Resources تحدد **شكل API فقط** — الاستعلامات في Model/Controller.
- لا تعرض حقولًا حساسة (`password`) في resources.
- متحكمات API يجب أن ترث `ApiController` وتستخدم `ok()` / `fail()` / `resource()`.

---

## وثائق ذات صلة

- [الاستجابات (Responses)](../basic/responses.md)
- [التصفح (Pagination)](../database/pagination.md)
- [التسلسل (Serialization)](./serialization.md)

---

[← العودة إلى الفهرس](../README.md)
