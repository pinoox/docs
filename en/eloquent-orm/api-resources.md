# API Resources

[← Back to index](../README.md)

Pinoox 3.x shapes JSON API output with **`Pinoox\Component\Http\Api\ApiResource`** (not Laravel JsonResource). Output is wrapped in the standard `{ success, data, message, meta }` envelope.

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

## Static Factory — `make()`

Create a resource instance with optional custom class:

```php
$resource = PostResource::make($post);
$resource = PostResource::make($post, CustomPostResource::class);
```

---

## Conditional Fields

### `when($condition, $value, $default)`

Include a field conditionally — accepts scalar, array, or callable:

```php
public function toArray(): array
{
    return [
        'id' => $this->resource->post_id,
        'title' => $this->resource->title,
        'published_at' => $this->when(
            $this->resource->status === 'published',
            fn() => $this->resource->published_at?->toIso8601String()
        ),
        'is_featured' => $this->when($this->resource->is_featured, true, false),
    ];
}
```

### `whenHas($key, $value)`

Include only when the key exists on the underlying resource:

```php
'description' => $this->whenHas('description', 'Custom description'),
// or use the actual value:
'description' => $this->whenHas('description'),
```

### `whenNotNull($value)`

Include only when value is not null:

```php
'category' => $this->whenNotNull($this->resource->category?->name),
```

---

## Relationships

### `whenLoaded($relation, $value, $default)`

Include a relation only if it was eager-loaded:

```php
public function toArray(): array
{
    return [
        'id' => $this->resource->post_id,
        'author' => $this->whenLoaded('author', [
            'id' => $this->resource->author->user_id,
            'name' => $this->resource->author->full_name,
        ]),
        'comments' => $this->whenLoaded('comments'),
    ];
}
```

### `whenCounted($relation, $key)`

Include relationship count if loaded:

```php
'comments_count' => $this->whenCounted('comments'),
'likes_count' => $this->whenCounted('likes', 'likes_count'),
```

### `includeRelation($relation, $callback)`

Include relation with optional transformation:

```php
'tags' => $this->includeRelation('tags', fn($tags) => $tags->pluck('name')),
```

---

## Array Helpers

### `merge(...$arrays)`

Merge arrays and remove null values:

```php
return $this->merge(
    ['id' => $this->resource->post_id],
    ['title' => $this->resource->title],
    $this->when($this->resource->status === 'published', [
        'published_at' => $this->resource->published_at?->toIso8601String(),
    ])
);
```

### `filter($data)`

Filter null values from an array:

```php
return $this->filter([
    'id' => $this->resource->post_id,
    'deleted_at' => $this->resource->deleted_at?->toIso8601String(),
]);
```

### `mergeWhen($condition, $value)`

Conditionally merge an array:

```php
return $this->merge(
    ['id' => $this->resource->post_id],
    $this->mergeWhen($this->resource->is_featured, ['featured' => true]),
);
```

---

## Dates

### `date($date)`

Transform a date to ISO 8601 string:

```php
'created_at' => $this->date($this->resource->created_at),
'published_at' => $this->date($this->resource->published_at),
```

---

## Additional Data

### `additional($data)` and `with()`

Add top-level data to the response:

```php
// In resource
public function with(): array
{
    return ['timestamp' => now()->toIso8601String()];
}

// Or dynamically:
$resource = (new PostResource($post))->additional(['view_count' => 100]);
```

---

## Pagination

### `paginator($paginator, $resourceClass)`

Transform a Laravel paginator with meta:

```php
$paginator = PostModel::paginate(15);

return $this->ok(
    PostResource::paginator($paginator, PostResource::class),
    'posts.loaded',
);

// Returns: { data: [...], meta: { current_page, last_page, per_page, total, from, to } }
```

### Manual pagination meta

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

## ResourceCollection

A collection class that wraps resources with optional pagination:

```php
use Pinoox\Component\Http\Api\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public $collects = PostResource::class;
}

// Usage in controller:
return $this->resource(new PostCollection($posts));
return $this->resource(PostCollection::fromPaginator($paginator, PostResource::class));
```

---

## Proxy Access

Access properties and methods directly on the underlying resource:

```php
public function toArray(): array
{
    // $this->title proxies to $this->resource->title
    return [
        'title' => $this->title,
        'upper_title' => strtoupper($this->title), // calls method on resource
    ];
}
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

## Tips

- Resources define **API shape only** — queries belong in Model/Controller.
- Do not expose sensitive fields (`password`) in resources.
- Use conditional methods to build flexible APIs based on request state.
- Use `make()` factory for cleaner instantiation.
- API controllers should extend `ApiController` and use `ok()` / `fail()` / `resource()`.

---

## Related docs

- [Responses](../basic/responses.md)
- [Pagination](../database/pagination.md)
- [Serialization](./serialization.md)

---

[← Back to index](../README.md)
