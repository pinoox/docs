# Eloquent Collections

[← Вернуться к оглавлению](../README.md)

Когда вы получаете несколько записей из модели (`get()`, `all()`), результат — это **`Illuminate\Database\Eloquent\Collection`**, а не обычный PHP-массив. Collections предоставляют мощные методы пакетной обработки.

---

## Получение collection

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

## load — eager load после запроса

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray и JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Применяются `$hidden` и `$visible` модели (см. [Serialization](./serialization.md)).

---

## Преобразование в Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` возвращает **`LengthAwarePaginator`**, а не Collection. Элементы:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // массив моделей
```

---

## Советы

- Для больших API используйте `map` + `ApiResource::collection()`.
- `each()` подходит для побочных эффектов без создания нового map.
- Collections не immutable — `transform()` изменяет значения на месте.

---

## Связанные документы

- [Eloquent — начало работы](./getting-started.md)
- [Serialization](./serialization.md)
- [API resources](./api-resources.md)

---

[← Вернуться к оглавлению](../README.md)
