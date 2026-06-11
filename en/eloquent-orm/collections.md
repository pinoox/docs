# Eloquent Collections

[← Back to index](../README.md)

When you fetch multiple records from a model (`get()`, `all()`), the result is an **`Illuminate\Database\Eloquent\Collection`** — not a plain PHP array. Collections provide powerful batch processing methods.

---

## Getting a collection

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

## load — eager load after query

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray and JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Model `$hidden` and `$visible` apply (see [Serialization](./serialization.md)).

---

## Convert to Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` returns a **`LengthAwarePaginator`**, not a Collection. Items:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array of models
```

---

## Tips

- For large APIs, use `map` + `ApiResource::collection()`.
- `each()` is good for side effects without creating a new map.
- Collections are not immutable — `transform()` changes values in place.

---

## Related docs

- [Eloquent getting started](./getting-started.md)
- [Serialization](./serialization.md)
- [API resources](./api-resources.md)

---

[← Back to index](../README.md)
