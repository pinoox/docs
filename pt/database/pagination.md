# Paginação

[← Voltar ao índice](../README.md)

O Pinoox 3.x suporta **`paginate()`** do Illuminate por meio do model Eloquent base do pincore. Para APIs, retorne o resultado no envelope padrão com campo **`meta`**.

---

## paginate em um model

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

`$posts` é um `LengthAwarePaginator`.

---

## paginate com Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## Parâmetros de paginate

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Query string padrão: `?page=2`

---

## simplePaginate e cursorPaginate

```php
PostModel::simplePaginate(15);      // sem contagem total — mais rápido
PostModel::cursorPaginate(15);      // para feeds infinitos
```

---

## Resposta de API com meta

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

## Com ApiResource

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

## Dicas

- Leia `per_page` da query string e limite (ex.: 100).
- Para listas grandes sem contagem total, `simplePaginate` é melhor.
- Aplique filtros **antes** de `paginate()`.

---

## Documentação relacionada

- [Query Builder](./query-builder.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Respostas](../basic/responses.md)

---

[← Voltar ao índice](../README.md)
