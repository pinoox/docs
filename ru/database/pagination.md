# Пагинация

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x поддерживает Illuminate **`paginate()`** через базовую Eloquent-модель pincore. Для API возвращайте результат в стандартной обёртке с полем **`meta`**.

---

## paginate на модели

```php
<?php
namespace App\com_acme_shop\Model;

use Pinoox\Component\Database\Model;

class PostModel extends Model
{
    protected $table = 'posts';
}
```

```php
use App\com_acme_shop\Model\PostModel;

$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->paginate(15);
```

`$posts` — это `LengthAwarePaginator`.

---

## paginate с Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## Параметры paginate

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Строка запроса по умолчанию: `?page=2`

---

## simplePaginate и cursorPaginate

```php
PostModel::simplePaginate(15);      // без общего количества — быстрее
PostModel::cursorPaginate(15);      // для бесконечных лент
```

---

## API-ответ с meta

```php
use Pinoox\Component\Kernel\Controller\ApiController;

class PostController extends ApiController
{
    public function index(Request $request)
    {
        $posts = PostModel::query()
            ->where('status', 'published')
            ->paginate((int) $request->get('per_page', 15));

        return $this->ok(
            data: $posts->items(),
            message: 'posts.loaded',
            meta: [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        );
    }
}
```

Обёртка:

```json
{
  "success": true,
  "data": [...],
  "message": "posts.loaded",
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 72
  }
}
```

---

## С ApiResource

```php
use App\com_acme_shop\Resource\PostResource;

$posts = PostModel::paginate(15);

return $this->ok(
    PostResource::collection($posts->items(), PostResource::class),
    meta: [
        'current_page' => $posts->currentPage(),
        'last_page' => $posts->lastPage(),
        'per_page' => $posts->perPage(),
        'total' => $posts->total(),
    ],
);
```

---

## Фронтенд (Vue)

```js
const { data, meta } = unwrapApiResponse(await postAPI.list({ page: 2, per_page: 15 }));
// meta.current_page, meta.last_page, ...
```

---

## Советы

- Читайте `per_page` из query string и ограничивайте (например, 100).
- Для больших списков без общего количества лучше `simplePaginate`.
- Применяйте фильтры **до** `paginate()`.

---

## Связанные документы

- [Query Builder](./query-builder.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Responses](../basic/responses.md)

---

[← Вернуться к оглавлению](../README.md)
