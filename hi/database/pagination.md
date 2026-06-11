# Pagination

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x pincore base Eloquent model के ज़रिए Illuminate **`paginate()`** support करता है। APIs के लिए standard envelope में **`meta`** field के साथ result return करें।

---

## Model पर paginate

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

`$posts` एक `LengthAwarePaginator` है।

---

## Query Builder के साथ paginate

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

## simplePaginate और cursorPaginate

```php
PostModel::simplePaginate(15);      // no total count — faster
PostModel::cursorPaginate(15);      // for infinite feeds
```

---

## meta के साथ API response

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

## ApiResource के साथ

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

- Query string से `per_page` read करें और cap करें (जैसे 100)।
- Total count के बिना large lists के लिए `simplePaginate` बेहतर है।
- Filters **`paginate()`** से **पहले** apply करें।

---

## संबंधित docs

- [Query Builder](./query-builder.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Responses](../basic/responses.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
