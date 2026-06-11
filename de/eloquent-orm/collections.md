# Eloquent Collections

[← Zurück zum Index](../README.md)

Wenn Sie mehrere Datensätze aus einem Model abrufen (`get()`, `all()`), ist das Ergebnis eine **`Illuminate\Database\Eloquent\Collection`** — kein einfaches PHP-Array. Collections bieten leistungsstarke Methoden zur Stapelverarbeitung.

---

## Collection erhalten

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

## load — Eager Loading nach der Abfrage

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray und JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Model-`$hidden` und `$visible` gelten (siehe [Serialisierung](./serialization.md)).

---

## In Support Collection umwandeln

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs. Collection

`paginate()` gibt einen **`LengthAwarePaginator`** zurück, keine Collection. Elemente:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // Array von Models
```

---

## Tipps

- Für große APIs `map` + `ApiResource::collection()` verwenden.
- `each()` eignet sich für Nebeneffekte ohne neues Map.
- Collections sind nicht unveränderlich — `transform()` ändert Werte vor Ort.

---

## Verwandte Dokumentation

- [Eloquent — Erste Schritte](./getting-started.md)
- [Serialisierung](./serialization.md)
- [API-Ressourcen](./api-resources.md)

---

[← Zurück zum Index](../README.md)
