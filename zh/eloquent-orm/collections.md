# Eloquent 集合

[← 返回索引](../README.md)

从模型获取多条记录（`get()`、`all()`）时，结果是 **`Illuminate\Database\Eloquent\Collection`** — 不是普通 PHP 数组。集合提供强大的批量处理方法。

---

## 获取集合

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

## load — 查询后预加载

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray 与 JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

模型的 `$hidden` 和 `$visible` 会生效（见[序列化](./serialization.md)）。

---

## 转换为 Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate 与 Collection

`paginate()` 返回 **`LengthAwarePaginator`**，不是 Collection。条目：

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // 模型数组
```

---

## 提示

- 大型 API 可使用 `map` + `ApiResource::collection()`。
- `each()` 适合产生副作用而不创建新 map。
- 集合不是不可变的 — `transform()` 会就地修改值。

---

## 相关文档

- [Eloquent 入门](./getting-started.md)
- [序列化](./serialization.md)
- [API 资源](./api-resources.md)

---

[← 返回索引](../README.md)
