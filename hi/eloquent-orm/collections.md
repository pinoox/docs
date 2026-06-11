# Eloquent Collections

[← इंडेक्स पर वापस जाएँ](../README.md)

Model से multiple records fetch (`get()`, `all()`) करने पर result **`Illuminate\Database\Eloquent\Collection`** होता है — plain PHP array नहीं। Collections powerful batch processing methods provide करती हैं।

---

## Collection प्राप्त करना

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

## load — query के बाद eager load

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray और JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Model `$hidden` और `$visible` apply होते हैं ([Serialization](./serialization.md) देखें)।

---

## Support Collection में convert

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` **`LengthAwarePaginator`** return करता है, Collection नहीं। Items:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array of models
```

---

## Tips

- Large APIs के लिए `map` + `ApiResource::collection()` उपयोग करें।
- Side effects के लिए `each()` अच्छा है बिना नया map बनाए।
- Collections immutable नहीं — `transform()` values in place बदलता है।

---

## संबंधित docs

- [Eloquent getting started](./getting-started.md)
- [Serialization](./serialization.md)
- [API resources](./api-resources.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
