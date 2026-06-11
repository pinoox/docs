# Sayfalama

[← Dizine dön](../README.md)

Pinoox 3.x, pincore temel Eloquent model'i üzerinden Illuminate **`paginate()`** destekler. API'ler için sonucu **`meta`** alanıyla standart zarfta döndürün.

---

## Model'de paginate

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

`$posts` bir `LengthAwarePaginator`'dır.

---

## Query Builder ile paginate

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## paginate parametreleri

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Varsayılan query string: `?page=2`

---

## simplePaginate ve cursorPaginate

```php
PostModel::simplePaginate(15);      // no total count — faster
PostModel::cursorPaginate(15);      // for infinite feeds
```

---

## meta ile API yanıtı

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

Zarf:

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

## ApiResource ile

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

## İpuçları

- `per_page` değerini query string'den okuyun ve sınırlayın (ör. 100).
- Toplam sayı olmadan büyük listeler için `simplePaginate` daha iyidir.
- Filtreleri `paginate()` **öncesinde** uygulayın.

---

## İlgili dokümantasyon

- [Query Builder](./query-builder.md)
- [API Resource'lar](../eloquent-orm/api-resources.md)
- [Response](../basic/responses.md)

---

[← Dizine dön](../README.md)
