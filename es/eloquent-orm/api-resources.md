# API Resources

[← Volver al índice](../README.md)

Pinoox 3.x da forma a la salida JSON de la API con **`Pinoox\Component\Http\Api\ApiResource`** (no JsonResource de Laravel). La salida se envuelve en el sobre estándar `{ success, data, message, meta }`.

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
// array de toArray() para cada elemento
```

---

## PayloadResource (array personalizado)

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## Sobre de respuesta

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

## meta para paginación

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

## Consejos

- Los resources definen **solo la forma de la API** — las consultas pertenecen a Model/Controller.
- No expongas campos sensibles (`password`) en resources.
- Los controllers API deben extender `ApiController` y usar `ok()` / `fail()` / `resource()`.

---

## Documentación relacionada

- [Responses](../basic/responses.md)
- [Paginación](../database/pagination.md)
- [Serialización](./serialization.md)

---

[← Volver al índice](../README.md)
