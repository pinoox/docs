# Ressources API

[← Retour à l'index](../README.md)

Pinoox 3.x façonne la sortie JSON des API avec **`Pinoox\Component\Http\Api\ApiResource`** (pas Laravel JsonResource). La sortie est encapsulée dans l'enveloppe standard `{ success, data, message, meta }`.

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

## Contrôleur

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
// tableau de toArray() pour chaque élément
```

---

## PayloadResource (tableau personnalisé)

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## Enveloppe de réponse

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

## meta pour la pagination

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

## Conseils

- Les ressources définissent **uniquement la forme API** — les requêtes appartiennent au Model/Controller.
- N'exposez pas de champs sensibles (`password`) dans les ressources.
- Les contrôleurs API doivent étendre `ApiController` et utiliser `ok()` / `fail()` / `resource()`.

---

## Documentation associée

- [Réponses](../basic/responses.md)
- [Pagination](../database/pagination.md)
- [Sérialisation](./serialization.md)

---

[← Retour à l'index](../README.md)
