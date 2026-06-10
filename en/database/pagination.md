# Pagination

[← Back to index](../../readme.md)

Pinoox 3.x supports Illuminate **`paginate()`** through the pincore base Eloquent model. For APIs, return the result in the standard envelope with a **`meta`** field.

---

## paginate on a model

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

`$posts` is a `LengthAwarePaginator`.

---

## paginate with Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## paginate parameters

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Default query string: `?page=2`

---

## simplePaginate and cursorPaginate

```php
PostModel::simplePaginate(15);      // no total count — faster
PostModel::cursorPaginate(15);      // for infinite feeds
```

---

## API response with meta

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

Envelope:

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

## With ApiResource

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

## Frontend (Vue)

```js
const { data, meta } = unwrapApiResponse(await postAPI.list({ page: 2, per_page: 15 }));
// meta.current_page, meta.last_page, ...
```

---

## Tips

- Read `per_page` from the query string and cap it (e.g. 100).
- For large lists without a total count, `simplePaginate` is better.
- Apply filters **before** `paginate()`.

---

## Related docs

- [Query Builder](./query-builder.md)
- [API resources](../eloquent-orm/api-resources.md)
- [API response](../../pinoox%20docs/pinoox-api-response.md)

---

[← Back to index](../../readme.md)
