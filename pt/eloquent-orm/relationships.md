# Relacionamentos Eloquent

[← Voltar ao índice](../README.md)

Relacionamentos Eloquent no Pinoox 3.x usam as APIs padrão **`hasMany`**, **`belongsTo`** e relacionadas. O exemplo abaixo é um blog simples dentro de um app Pinoox:

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
$comments = $post->comments;   // query separada
$author = $post->author;
```

---

## Criar por meio de uma relação

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

A tabela pivot `post_tags` também recebe o prefixo do app.

---

## Dicas

- FK para `UserModel` do núcleo: `user_id` → `pincore_user.user_id`.
- Sempre use `with()` em listas de API para evitar queries N+1.
- Use nomes de relação singulares/plurais padrão do Eloquent.

---

## Documentação relacionada

- [Primeiros passos com Eloquent](./getting-started.md)
- [Coleções](./collections.md)
- [Serialização](./serialization.md)

---

[← Voltar ao índice](../README.md)
