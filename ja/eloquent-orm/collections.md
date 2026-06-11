# Eloquent Collections

[← 索引に戻る](../README.md)

Model から複数レコードを取得（`get()`、`all()`）すると、結果は **`Illuminate\Database\Eloquent\Collection`** です — 通常の PHP 配列ではありません。Collection は強力なバッチ処理メソッドを提供します。

---

## Collection の取得

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

## load — クエリ後の eager load

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray と JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Model の `$hidden` と `$visible` が適用されます（[Serialization](./serialization.md) 参照）。

---

## Support Collection への変換

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` は **`LengthAwarePaginator`** を返し、Collection ではありません。アイテム:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // Model の配列
```

---

## ヒント

- 大きな API には `map` + `ApiResource::collection()` を使用。
- 新しい map を作らず副作用には `each()` が適する。
- Collection は不変ではない — `transform()` はその場で値を変更。

---

## 関連ドキュメント

- [Eloquent はじめに](./getting-started.md)
- [Serialization](./serialization.md)
- [API Resources](./api-resources.md)

---

[← 索引に戻る](../README.md)
