# Paginierung

[← Zurück zum Index](../README.md)

Pinoox 3.x unterstützt Illuminate-**`paginate()`** über das pincore-Basis-Eloquent-Model. Für APIs das Ergebnis im Standard-Envelope mit einem **`meta`**-Feld zurückgeben.

---

## paginate auf einem Model

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

`$posts` ist ein `LengthAwarePaginator`.

---

## paginate mit Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## paginate-Parameter

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Standard-Query-String: `?page=2`

---

## simplePaginate und cursorPaginate

```php
PostModel::simplePaginate(15);      // ohne Gesamtanzahl — schneller
PostModel::cursorPaginate(15);      // für unendliche Feeds
```

---

## API-Response mit meta

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

## Mit ApiResource

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

## Tipps

- `per_page` aus dem Query-String lesen und begrenzen (z. B. 100).
- Für große Listen ohne Gesamtanzahl ist `simplePaginate` besser.
- Filter **vor** `paginate()` anwenden.

---

## Verwandte Dokumentation

- [Query Builder](./query-builder.md)
- [API-Ressourcen](../eloquent-orm/api-resources.md)
- [Responses](../basic/responses.md)

---

[← Zurück zum Index](../README.md)
