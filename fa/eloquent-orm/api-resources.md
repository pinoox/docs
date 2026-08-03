# منابع API (ApiResource)

[← بازگشت به فهرست](../README.md)

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

## کارخانه استاتیک — `make()`

ایجاد نمونه از resource با کلاس سفارشی:

```php
$resource = PostResource::make($post);
$resource = PostResource::make($post, CustomPostResource::class);
```

---

## فیلدهای شرطی

### `when($condition, $value, $default)`

شامل کردن فیلد به صورت شرطی — مقدار می‌تواند scalar، آرایه یا callable باشد:

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

شامل کردن فقط زمانی که کلید در resource وجود دارد:

```php
'description' => $this->whenHas('description', 'توضیحات سفارشی'),
// یا استفاده از مقدار واقعی:
'description' => $this->whenHas('description'),
```

### `whenNotNull($value)`

شامل کردن فقط زمانی که مقدار null نیست:

```php
'category' => $this->whenNotNull($this->resource->category?->name),
```

---

## روابط

### `whenLoaded($relation, $value, $default)`

شامل کردن رابطه فقط زمانی که eager-load شده:

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

شامل کردن تعداد رابطه در صورت لود شدن:

```php
'comments_count' => $this->whenCounted('comments'),
'likes_count' => $this->whenCounted('likes', 'likes_count'),
```

### `includeRelation($relation, $callback)`

شامل کردن رابطه با تبدیل اختیاری:

```php
'tags' => $this->includeRelation('tags', fn($tags) => $tags->pluck('name')),
```

---

## کمک‌های آرایه

### `merge(...$arrays)`

ادغام آرایه‌ها و حذف مقادیر null:

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

فیلتر کردن مقادیر null از آرایه:

```php
return $this->filter([
    'id' => $this->resource->post_id,
    'deleted_at' => $this->resource->deleted_at?->toIso8601String(),
]);
```

### `mergeWhen($condition, $value)`

شرطی ادغام کردن آرایه:

```php
return $this->merge(
    ['id' => $this->resource->post_id],
    $this->mergeWhen($this->resource->is_featured, ['featured' => true]),
);
```

---

## تاریخ‌ها

### `date($date)`

تبدیل تاریخ به رشته ISO 8601:

```php
'created_at' => $this->date($this->resource->created_at),
'published_at' => $this->date($this->resource->published_at),
```

---

## داده‌های اضافی

### `additional($data)` و `with()`

اضافه کردن داده‌های سطح بالا به پاسخ:

```php
// در resource
public function with(): array
{
    return ['timestamp' => now()->toIso8601String()];
}

// یا پویا:
$resource = (new PostResource($post))->additional(['view_count' => 100]);
```

---

## صفحه‌بندی

### `paginator($paginator, $resourceClass)`

تبدیل paginator لاراول با meta:

```php
$paginator = PostModel::paginate(15);

return $this->ok(
    PostResource::paginator($paginator, PostResource::class),
    'posts.loaded',
);

// برمی‌گرداند: { data: [...], meta: { current_page, last_page, per_page, total, from, to } }
```

### meta دستی صفحه‌بندی

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

کلاسی برای مجموعه resourceها با صفحه‌بندی اختیاری:

```php
use Pinoox\Component\Http\Api\ResourceCollection;

class PostCollection extends ResourceCollection
{
    public $collects = PostResource::class;
}

// استفاده در کنترلر:
return $this->resource(new PostCollection($posts));
return $this->resource(PostCollection::fromPaginator($paginator, PostResource::class));
```

---

## دسترسی پروکسی

دسترسی مستقیم به property و methodهای resource زیرین:

```php
public function toArray(): array
{
    // $this->title پروکسی می‌شود به $this->resource->title
    return [
        'title' => $this->title,
        'upper_title' => strtoupper($this->title), // فراخوانی متد روی resource
    ];
}
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

## نکات

- Resource فقط **شکل API** را تعریف می‌کند — query در Model/Controller.
- فیلدهای حساس (`password`) را در Resource expose نکنید.
- از متدهای شرطی برای ساخت APIهای انعطاف‌پذیر بر اساس state درخواست استفاده کنید.
- از کارخانه `make()` برای نمونه‌سازی تمیزتر استفاده کنید.
- کنترلر API از `ApiController` ارث ببرد و `ok()` / `fail()` / `resource()` استفاده کند.

---

## مستندات مرتبط

- [پاسخ — Responses](../basic/responses.md)
- [صفحه‌بندی](../database/pagination.md)
- [سریال‌سازی](./serialization.md)

---

[← بازگشت به فهرست](../README.md)
