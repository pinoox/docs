# Eloquent-Beziehungen

[← Zurück zum Index](../README.md)

Eloquent-Beziehungen in Pinoox 3.x verwenden die Standard-APIs **`hasMany`**, **`belongsTo`** und verwandte Methoden. Das folgende Beispiel ist ein einfacher Blog innerhalb einer Pinoox-App:

---

## Models

```php
<?php
namespace App\com_acme_blog\Model;

use Pinoox\Component\Database\Model;
use Pinoox\Model\UserModel;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';

    protected $fillable = ['user_id', 'title', 'body', 'status'];

    public function author()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(CommentModel::class, 'post_id', 'post_id');
    }
}
```

```php
<?php
namespace App\com_acme_blog\Model;

class CommentModel extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';

    protected $fillable = ['post_id', 'user_id', 'body'];

    public function post()
    {
        return $this->belongsTo(PostModel::class, 'post_id', 'post_id');
    }

    public function author()
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }
}
```

---

## Eager Loading

```php
$posts = PostModel::with(['author', 'comments'])->get();

foreach ($posts as $post) {
    echo $post->author->email;
    echo $post->comments->count();
}
```

---

## Lazy Loading

```php
$post = PostModel::find(1);
$comments = $post->comments;   // separate Abfrage
$author = $post->author;
```

---

## Über eine Beziehung erstellen

```php
$post = PostModel::find(1);

$post->comments()->create([
    'user_id' => Auth::id(),
    'body' => 'New comment',
]);
```

---

## whereHas

```php
$posts = PostModel::whereHas('comments', function ($q) {
    $q->where('created_at', '>=', now()->subDay());
})->get();
```

---

## belongsToMany (optional)

```php
public function tags()
{
    return $this->belongsToMany(
        TagModel::class,
        'post_tags',
        'post_id',
        'tag_id',
    );
}
```

Die Pivot-Tabelle `post_tags` erhält ebenfalls das App-Präfix.

---

## Tipps

- FK zu Core-`UserModel`: `user_id` → `pincore_user.user_id`.
- Für API-Listen immer `with()` verwenden, um N+1-Abfragen zu vermeiden.
- Standardmäßige Eloquent-Singular-/Plural-Beziehungsnamen verwenden.

---

## Verwandte Dokumentation

- [Eloquent — Erste Schritte](./getting-started.md)
- [Collections](./collections.md)
- [Serialisierung](./serialization.md)

---

[← Zurück zum Index](../README.md)
