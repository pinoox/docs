# 分页（Pagination）

[← 返回索引](../README.md)

Pinoox 3.x 通过 pincore 的 Eloquent 基础模型支持 Illuminate 的 **`paginate()`**。对于 API，请用带 **`meta`** 字段的标准信封结构返回结果。

---

## 在模型上分页

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

`$posts` 是一个 `LengthAwarePaginator`。

---

## 使用 Query Builder 分页

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## paginate 参数

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

默认查询字符串：`?page=2`

---

## simplePaginate 与 cursorPaginate

```php
PostModel::simplePaginate(15);      // 不统计总数 —— 更快
PostModel::cursorPaginate(15);      // 适用于无限滚动列表
```

---

## 带 meta 的 API 响应

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

信封结构：

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

## 配合 ApiResource 使用

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

## 前端（Vue）

```js
const { data, meta } = unwrapApiResponse(await postAPI.list({ page: 2, per_page: 15 }));
// meta.current_page, meta.last_page, ...
```

---

## 提示

- 从查询字符串读取 `per_page` 并设置上限（例如 100）。
- 对于不需要总数的大列表，`simplePaginate` 更合适。
- 在调用 `paginate()` **之前** 应用过滤条件。

---

## 相关文档

- [查询构建器（Query Builder）](./query-builder.md)
- [API 资源（API resources）](../eloquent-orm/api-resources.md)
- [响应（Responses）](../basic/responses.md)

---

[← 返回索引](../README.md)
