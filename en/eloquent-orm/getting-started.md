# Eloquent ORM Getting Started

[← Back to index](../../readme.md)

App models live in **`apps/{package}/Model/`** and extend **`Pinoox\Component\Database\Model`**. That is Pinoox’s base class: it wraps Eloquent with automatic app connection and table prefix handling.

---

## Create a model

```bash
php pinoox model:create Post com_acme_blog
```

```php
<?php
namespace App\com_acme_blog\Model;

use Pinoox\Component\Database\Model;

class PostModel extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'post_id';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'user_id', 'title', 'body', 'status',
    ];
}
```

The physical table name is resolved via `DB::tableNameForModel()` — the app prefix is applied automatically.

---

## Table constants

```php
<?php
namespace App\com_acme_blog\Model;

final class Table
{
    public const POSTS = 'posts';
    public const COMMENTS = 'comments';
}
```

```php
protected $table = Table::POSTS;
```

---

## Basic CRUD

```php
use App\com_acme_blog\Model\PostModel;

$post = PostModel::find(1);
$post = PostModel::where('status', 'published')->first();
$all = PostModel::where('user_id', 5)->get();

$post = PostModel::create([
    'title' => 'Hello Pinoox',
    'body' => '...',
    'status' => 'draft',
    'user_id' => Auth::id(),
]);

$post->update(['status' => 'published']);
$post->delete();
```

---

## Query scopes (chainable)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Database connection

The model automatically picks the app connection from its namespace:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

For manual queries:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Table prefix — reminder

| Scenario | Table `posts` |
|----------|---------------|
| Shared DB, `com_acme_blog` | `blog_posts` (prefix from package) |
| Dedicated DB, empty prefix | `posts` |
| Explicit prefix `shop_` | `shop_posts` |
| Core | `pincore_user`, etc. |

---

## Transaction on a model

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Tips

- Models belong in the app `Model/` folder — not in pincore (unless you fork the framework).
- Define `$fillable` or `$guarded`.
- For core tables, use `Pinoox\Model\UserModel` and other pincore models.

---

## Related docs

- [Database getting started](../database/getting-started.md)
- [Relationships](./relationships.md)
- [Migrations](../database/migrations.md)

---

[← Back to index](../../readme.md)
