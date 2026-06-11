# Collections Eloquent

[← Retour à l'index](../README.md)

Lorsque vous récupérez plusieurs enregistrements d'un modèle (`get()`, `all()`), le résultat est une **`Illuminate\Database\Eloquent\Collection`** — pas un tableau PHP simple. Les collections offrent des méthodes puissantes de traitement par lot.

---

## Obtenir une collection

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

## load — eager load après requête

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray et JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

Les `$hidden` et `$visible` du modèle s'appliquent (voir [Sérialisation](./serialization.md)).

---

## Convertir en Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` renvoie un **`LengthAwarePaginator`**, pas une Collection. Éléments :

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // tableau de modèles
```

---

## Conseils

- Pour les grosses API, utilisez `map` + `ApiResource::collection()`.
- `each()` convient aux effets de bord sans créer un nouveau map.
- Les collections ne sont pas immuables — `transform()` modifie les valeurs sur place.

---

## Documentation associée

- [Premiers pas Eloquent](./getting-started.md)
- [Sérialisation](./serialization.md)
- [Ressources API](./api-resources.md)

---

[← Retour à l'index](../README.md)
