# API Resources

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x формирует JSON-вывод API с помощью **`Pinoox\Component\Http\Api\ApiResource`** (не Laravel JsonResource). Вывод оборачивается в стандартную обёртку `{ success, data, message, meta }`.

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
// массив toArray() для каждого элемента
```

---

## PayloadResource (пользовательский массив)

```php
use Pinoox\Component\Http\Api\PayloadResource;

return $this->resource(new PayloadResource([
    'connected' => true,
    'version' => '3.1',
]));
```

---

## Обёртка ответа

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

## meta для пагинации

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

## Советы

- Resources определяют **только форму API** — запросы принадлежат Model/Controller.
- Не раскрывайте чувствительные поля (`password`) в resources.
- API-контроллеры должны наследовать `ApiController` и использовать `ok()` / `fail()` / `resource()`.

---

## Связанные документы

- [Responses](../basic/responses.md)
- [Пагинация](../database/pagination.md)
- [Serialization](./serialization.md)

---

[← Вернуться к оглавлению](../README.md)
