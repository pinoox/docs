# Paginación

[← Volver al índice](../README.md)

Pinoox 3.x soporta **`paginate()`** de Illuminate mediante el modelo Eloquent base de pincore. Para APIs, devuelve el resultado en el sobre estándar con un campo **`meta`**.

---

## paginate en un modelo

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

`$posts` es un `LengthAwarePaginator`.

---

## paginate con Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## Parámetros de paginate

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Cadena de consulta por defecto: `?page=2`

---

## simplePaginate y cursorPaginate

```php
PostModel::simplePaginate(15);      // sin conteo total — más rápido
PostModel::cursorPaginate(15);      // para feeds infinitos
```

---

## Respuesta API con meta

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

Sobre:

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

## Con ApiResource

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

## Consejos

- Lee `per_page` desde la cadena de consulta y pon un tope (p. ej. 100).
- Para listas grandes sin conteo total, `simplePaginate` es mejor.
- Aplica filtros **antes** de `paginate()`.

---

## Documentación relacionada

- [Query Builder](./query-builder.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Responses](../basic/responses.md)

---

[← Volver al índice](../README.md)
