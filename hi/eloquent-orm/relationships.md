# Eloquent Relationships

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x में Eloquent relationships standard **`hasMany`**, **`belongsTo`**, और related APIs उपयोग करती हैं। नीचे example Pinoox app के अंदर simple blog है:

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
$comments = $post->comments;   // separate query
$author = $post->author;
```

---

## Relation के ज़रिए create

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

Pivot table `post_tags` भी app prefix receive करता है।

---

## Tips

- Core `UserModel` पर FK: `user_id` → `pincore_user.user_id`.
- N+1 queries avoid करने के लिए API lists पर हमेशा `with()` उपयोग करें।
- Standard Eloquent singular/plural relation names उपयोग करें।

---

## संबंधित docs

- [Eloquent getting started](./getting-started.md)
- [Collections](./collections.md)
- [Serialization](./serialization.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
