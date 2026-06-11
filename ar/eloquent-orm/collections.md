# مجموعات Eloquent (Collections)

[← العودة إلى الفهرس](../README.md)

عند جلب سجلات متعددة من نموذج (`get()`، `all()`)، النتيجة **`Illuminate\Database\Eloquent\Collection`** — وليست مصفوفة PHP عادية. توفر Collections طرق معالجة دفعية قوية.

---

## الحصول على collection

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

## load — eager load بعد الاستعلام

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

`$hidden` و`$visible` على النموذج تنطبق (راجع [التسلسل (Serialization)](./serialization.md)).

---

## التحويل إلى Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate مقابل Collection

`paginate()` يُرجع **`LengthAwarePaginator`**، وليس Collection. العناصر:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array of models
```

---

## نصائح

- لـ APIs كبيرة، استخدم `map` + `ApiResource::collection()`.
- `each()` جيد للآثار الجانبية دون إنشاء map جديد.
- Collections ليست immutable — `transform()` يغيّر القيم في مكانها.

---

## وثائق ذات صلة

- [البدء مع Eloquent](./getting-started.md)
- [التسلسل (Serialization)](./serialization.md)
- [موارد API (API resources)](./api-resources.md)

---

[← العودة إلى الفهرس](../README.md)
