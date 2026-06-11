# Eloquent Relationships

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x Eloquent relationship은 표준 **`hasMany`**, **`belongsTo`** 등 API를 사용합니다. 아래 예제는 Pinoox 앱 내부의 simple blog입니다:

---

## Model

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
$comments = $post->comments;   // separate query
$author = $post->author;
```

---

## Relation을 통한 create

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

## belongsToMany (선택)

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

Pivot table `post_tags`에도 앱 prefix가 적용됩니다.

---

## Tips

- core `UserModel` FK: `user_id` → `pincore_user.user_id`.
- N+1 query 방지를 위해 API list에는 항상 `with()` 사용.
- 표준 Eloquent singular/plural relation 이름 사용.

---

## 관련 문서

- [Eloquent 시작하기](./getting-started.md)
- [Collections](./collections.md)
- [Serialization](./serialization.md)

---

[← 색인으로 돌아가기](../README.md)
