# مجموعه‌های Eloquent (Collections)

[← بازگشت به فهرست](../README.md)

وقتی از Model چند رکورد می‌گیرید (`get()`, `all()`)، نتیجه **`Illuminate\Database\Eloquent\Collection`** است — نه آرایه ساده PHP. این مجموعه متدهای قدرتمند برای پردازش دسته‌ای دارد.

---

## دریافت Collection

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

## load — eager load بعد از query

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray و JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

`$hidden` و `$visible` روی Model اعمال می‌شود (ر.ک. [سریال‌سازی](./serialization.md)).

---

## تبدیل به Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` یک **`LengthAwarePaginator`** برمی‌گرداند، نه Collection. آیتم‌ها:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array of models
```

---

## نکات

- برای API بزرگ از `map` + `ApiResource::collection()` استفاده کنید.
- `each()` برای side effect بدون map جدید مناسب است.
- Collection immutable نیست — `transform()` مقادیر را in-place عوض می‌کند.

---

## مستندات مرتبط

- [شروع به کار Eloquent](./getting-started.md)
- [سریال‌سازی](./serialization.md)
- [منابع API](./api-resources.md)

---

[← بازگشت به فهرست](../README.md)
