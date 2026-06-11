# Relaciones Eloquent

[← Volver al índice](../README.md)

Las relaciones Eloquent en Pinoox 3.x usan las APIs estándar **`hasMany`**, **`belongsTo`** y relacionadas. El ejemplo siguiente es un blog simple dentro de una app Pinoox:

---

## Modelos

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

## Eager loading

```php
$posts = PostModel::with(['author', 'comments'])->get();

foreach ($posts as $post) {
    echo $post->author->email;
    echo $post->comments->count();
}
```

---

## Lazy loading

```php
$post = PostModel::find(1);
$comments = $post->comments;   // consulta separada
$author = $post->author;
```

---

## Crear a través de una relación

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

## belongsToMany (opcional)

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

La tabla pivote `post_tags` también recibe el prefijo de la app.

---

## Consejos

- FK a `UserModel` del núcleo: `user_id` → `pincore_user.user_id`.
- Usa siempre `with()` en listas API para evitar consultas N+1.
- Usa nombres de relación Eloquent estándar singular/plural.

---

## Documentación relacionada

- [Primeros pasos Eloquent](./getting-started.md)
- [Colecciones](./collections.md)
- [Serialización](./serialization.md)

---

[← Volver al índice](../README.md)
