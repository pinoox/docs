# Eloquent Collection'lar

[← Dizine dön](../README.md)

Bir model'den birden fazla kayıt çektiğinizde (`get()`, `all()`), sonuç düz bir PHP dizisi değil **`Illuminate\Database\Eloquent\Collection`**'dır. Collection'lar güçlü toplu işleme metotları sağlar.

---

## Collection alma

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

## load — sorgudan sonra eager load

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray ve JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Model `$hidden` ve `$visible` uygulanır (bkz. [Serileştirme](./serialization.md)).

---

## Support Collection'a dönüştürme

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate ve Collection

`paginate()` bir **`LengthAwarePaginator`** döndürür, Collection değil. Öğeler:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array of models
```

---

## İpuçları

- Büyük API'ler için `map` + `ApiResource::collection()` kullanın.
- Yeni map oluşturmadan yan etkiler için `each()` iyidir.
- Collection'lar değiştirilebilir — `transform()` değerleri yerinde değiştirir.

---

## İlgili dokümantasyon

- [Eloquent'e başlarken](./getting-started.md)
- [Serileştirme](./serialization.md)
- [API Resource'lar](./api-resources.md)

---

[← Dizine dön](../README.md)
