# Eloquent Collections

[← 색인으로 돌아가기](../README.md)

Model에서 여러 record를 가져오면(`get()`, `all()`) 결과는 plain PHP array가 아닌 **`Illuminate\Database\Eloquent\Collection`**입니다. Collection은 강력한 batch processing method를 제공합니다.

---

## Collection 가져오기

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

## load — query 후 eager load

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray와 JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Model `$hidden`과 `$visible`이 적용됩니다 ([Serialization](./serialization.md) 참조).

---

## Support Collection으로 변환

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()`는 Collection이 아닌 **`LengthAwarePaginator`**를 반환합니다. Items:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array of models
```

---

## Tips

- 큰 API에는 `map` + `ApiResource::collection()` 사용.
- 새 map 없이 side effect에는 `each()`가 좋음.
- Collection은 immutable 아님 — `transform()`이 값을 제자리에서 변경.

---

## 관련 문서

- [Eloquent 시작하기](./getting-started.md)
- [Serialization](./serialization.md)
- [API resources](./api-resources.md)

---

[← 색인으로 돌아가기](../README.md)
