# Pagination

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x는 pincore base Eloquent model을 통해 Illuminate **`paginate()`**를 지원합니다. API에서는 표준 envelope과 **`meta`** field로 결과를 반환하세요.

---

## Model에서 paginate

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

`$posts`는 `LengthAwarePaginator`입니다.

---

## Query Builder로 paginate

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## paginate parameter

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

## simplePaginate와 cursorPaginate

```php
PostModel::simplePaginate(15);      // no total count — faster
PostModel::cursorPaginate(15);      // for infinite feeds
```

---

## meta가 있는 API response

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

## ApiResource와 함께

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

- query string에서 `per_page`를 읽고 상한 설정 (예: 100)
- total count 없이 큰 목록에는 `simplePaginate`가 더 좋음
- filter는 **`paginate()` 전에** 적용

---

## 관련 문서

- [Query Builder](./query-builder.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Responses](../basic/responses.md)

---

[← 색인으로 돌아가기](../README.md)
