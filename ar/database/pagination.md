# التصفح (Pagination)

[← العودة إلى الفهرس](../README.md)

يدعم Pinoox 3.x **`paginate()`** من Illuminate عبر نموذج Eloquent الأساسي في pincore. لـ APIs، أرجع النتيجة في الغلاف المعياري مع حقل **`meta`**.

---

## paginate على نموذج

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

`$posts` هو `LengthAwarePaginator`.

---

## paginate مع Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## معاملات paginate

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

سلسلة الاستعلام الافتراضية: `?page=2`

---

## simplePaginate و cursorPaginate

```php
PostModel::simplePaginate(15);      // no total count — faster
PostModel::cursorPaginate(15);      // for infinite feeds
```

---

## استجابة API مع meta

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

الغلاف:

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

## مع ApiResource

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

## الواجهة الأمامية (Vue)

```js
const { data, meta } = unwrapApiResponse(await postAPI.list({ page: 2, per_page: 15 }));
// meta.current_page, meta.last_page, ...
```

---

## نصائح

- اقرأ `per_page` من سلسلة الاستعلام وحدّده (مثل 100).
- للقوائم الكبيرة بدون العدد الإجمالي، `simplePaginate` أفضل.
- طبّق المرشحات **قبل** `paginate()`.

---

## وثائق ذات صلة

- [Query Builder](./query-builder.md)
- [موارد API](../eloquent-orm/api-resources.md)
- [الاستجابات (Responses)](../basic/responses.md)

---

[← العودة إلى الفهرس](../README.md)
