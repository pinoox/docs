# Colecciones Eloquent

[← Volver al índice](../README.md)

Cuando obtienes varios registros de un modelo (`get()`, `all()`), el resultado es una **`Illuminate\Database\Eloquent\Collection`** — no un array PHP plano. Las colecciones ofrecen métodos potentes de procesamiento por lotes.

---

## Obtener una colección

```php
use App\com_acme_blog\Model\PostModel;

$posts = PostModel::where('status', 'published')->get();
// Illuminate\Database\Eloquent\Collection
```

---

## map

```php
$titles = $posts->map(fn ($post) => $post->title);

$items = $posts->map(fn ($post) => [
    'id' => $post->post_id,
    'title' => $post->title,
]);
```

---

## filter

```php
$recent = $posts->filter(fn ($post) => $post->created_at->isToday());

$published = $posts->where('status', 'published');
```

---

## pluck

```php
$ids = $posts->pluck('post_id');
$titlesById = $posts->pluck('title', 'post_id');
```

---

## keyBy / groupBy

```php
$byUser = $posts->groupBy('user_id');

$indexed = $posts->keyBy('post_id');
```

---

## first / last / isEmpty

```php
$first = $posts->first();
$last = $posts->last();

if ($posts->isEmpty()) {
    // ...
}
```

---

## load — eager load tras la consulta

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray y JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

`$hidden` y `$visible` del modelo se aplican (consulta [Serialización](./serialization.md)).

---

## Convertir a Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` devuelve un **`LengthAwarePaginator`**, no una Collection. Elementos:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array de modelos
```

---

## Consejos

- Para APIs grandes, usa `map` + `ApiResource::collection()`.
- `each()` es bueno para efectos secundarios sin crear un map nuevo.
- Las colecciones no son inmutables — `transform()` cambia valores in situ.

---

## Documentación relacionada

- [Primeros pasos Eloquent](./getting-started.md)
- [Serialización](./serialization.md)
- [API resources](./api-resources.md)

---

[← Volver al índice](../README.md)
