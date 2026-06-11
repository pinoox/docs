# Relations Eloquent

[← Retour à l'index](../README.md)

Les relations Eloquent dans Pinoox 3.x utilisent les API standard **`hasMany`**, **`belongsTo`**, etc. L'exemple ci-dessous est un blog simple dans une app Pinoox :

---

## Modèles

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
$comments = $post->comments;   // requête séparée
$author = $post->author;
```

---

## Créer via une relation

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

## belongsToMany (optionnel)

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

La table pivot `post_tags` reçoit aussi le préfixe de l'app.

---

## Conseils

- FK vers le `UserModel` du cœur : `user_id` → `pincore_user.user_id`.
- Utilisez toujours `with()` pour les listes API afin d'éviter les requêtes N+1.
- Utilisez les noms de relation singulier/pluriel Eloquent standard.

---

## Documentation associée

- [Premiers pas Eloquent](./getting-started.md)
- [Collections](./collections.md)
- [Sérialisation](./serialization.md)

---

[← Retour à l'index](../README.md)
