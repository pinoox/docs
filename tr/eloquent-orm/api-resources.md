# API Resource'lar

[← Dizine dön](../README.md)

Pinoox 3.x JSON API çıktısını **`Pinoox\Component\Http\Api\ApiResource`** ile şekillendirir (Laravel JsonResource değil). Çıktı standart `{ success, data, message, meta }` zarfına sarılır.

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

## PayloadResource (özel dizi)

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## Yanıt zarfı

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

## Sayfalama için meta

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

## İpuçları

- Resource'lar yalnızca **API şeklini** tanımlar — sorgular Model/Controller'da olmalıdır.
- Resource'larda hassas alanları (`password`) açığa çıkarmayın.
- API controller'ları `ApiController`'ı genişletmeli ve `ok()` / `fail()` / `resource()` kullanmalıdır.

---

## İlgili dokümantasyon

- [Response](../basic/responses.md)
- [Sayfalama](../database/pagination.md)
- [Serileştirme](./serialization.md)

---

[← Dizine dön](../README.md)
