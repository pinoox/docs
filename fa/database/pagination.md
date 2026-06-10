# صفحه‌بندی (Pagination)

پینوکس 3.x از **`paginate()`** Illuminate (از طریق Eloquent Model پایه pincore) پشتیبانی می‌کند. برای API، نتیجه را در envelope استاندارد با فیلد **`meta`** برگردانید.

---

## paginate در Model

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

`$posts` از نوع `LengthAwarePaginator` است.

---

## paginate در Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## پارامترهای paginate

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Query string پیش‌فرض: `?page=2`

---

## simplePaginate و cursorPaginate

```php
PostModel::simplePaginate(15);      // بدون total count — سریع‌تر
PostModel::cursorPaginate(15);      // برای فید بی‌نهایت
```

---

## پاسخ API با meta

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

## با ApiResource

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

## فرانت‌اند (Vue)

```js
const { data, meta } = unwrapApiResponse(await postAPI.list({ page: 2, per_page: 15 }));
// meta.current_page, meta.last_page, ...
```

---

## نکات

- `per_page` را از query string بگیرید و سقف (مثلاً 100) بگذارید.
- برای لیست‌های بزرگ بدون شمارش کل، `simplePaginate` بهتر است.
- فیلترها را **قبل از** `paginate()` اعمال کنید.

---

## مستندات مرتبط

- [Query Builder](./query-builder.md)
- [منابع API](../eloquent-orm/api-resources.md)
- [API Response](../../pinoox%20docs/pinoox-api-response.md)
