# Pagination

[← Retour à l'index](../README.md)

Pinoox 3.x prend en charge **`paginate()`** d'Illuminate via le modèle Eloquent de base de pincore. Pour les API, retournez le résultat dans l'enveloppe standard avec un champ **`meta`**.

---

## paginate sur un modèle

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

`$posts` est un `LengthAwarePaginator`.

---

## paginate avec le Query Builder

```php
use Pinoox\Portal\Database\DB;

$page = DB::app()->table('orders')
    ->where('status', 'paid')
    ->orderByDesc('order_id')
    ->paginate(20);
```

---

## Paramètres de paginate

```php
PostModel::paginate(
    perPage: 15,
    columns: ['*'],
    pageName: 'page',
    page: $request->get('page', 1),
);
```

Chaîne de requête par défaut : `?page=2`

---

## simplePaginate et cursorPaginate

```php
PostModel::simplePaginate(15);      // pas de comptage total — plus rapide
PostModel::cursorPaginate(15);      // pour les flux infinis
```

---

## Réponse API avec meta

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

Enveloppe :

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

## Avec ApiResource

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

## Conseils

- Lisez `per_page` depuis la chaîne de requête et plafonnez-le (par ex. 100).
- Pour les grandes listes sans comptage total, `simplePaginate` est préférable.
- Appliquez les filtres **avant** `paginate()`.

---

## Documentation associée

- [Query Builder](./query-builder.md)
- [Ressources API](../eloquent-orm/api-resources.md)
- [Réponses](../basic/responses.md)

---

[← Retour à l'index](../README.md)
