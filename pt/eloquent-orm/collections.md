# Coleções Eloquent

[← Voltar ao índice](../README.md)

Quando você busca vários registros de um model (`get()`, `all()`), o resultado é uma **`Illuminate\Database\Eloquent\Collection`** — não um array PHP comum. Coleções oferecem métodos poderosos de processamento em lote.

---

## Obter uma coleção

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

## load — eager load após a query

```php
$posts = PostModel::all();
$posts->load('comments', 'author');
```

---

## toArray e JSON

```php
$array = $posts->toArray();
$json = $posts->toJson();
```

`$hidden` e `$visible` do model se aplicam (veja [Serialização](./serialization.md)).

---

## Converter para Support Collection

```php
use Illuminate\Support\Collection;

$support = Collection::make($posts);
```

---

## paginate vs Collection

`paginate()` retorna um **`LengthAwarePaginator`**, não uma Collection. Itens:

```php
$paginator = PostModel::paginate(15);
$items = $paginator->items();   // array de models
```

---

## Dicas

- Para APIs grandes, use `map` + `ApiResource::collection()`.
- `each()` é bom para efeitos colaterais sem criar um novo map.
- Coleções não são imutáveis — `transform()` altera valores no lugar.

---

## Documentação relacionada

- [Primeiros passos com Eloquent](./getting-started.md)
- [Serialização](./serialization.md)
- [API resources](./api-resources.md)

---

[← Voltar ao índice](../README.md)
