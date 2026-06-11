# Eloquent ORM Getting Started

[← इंडेक्स पर वापस जाएँ](../README.md)

App models **`apps/{package}/Model/`** में रहती हैं और **`Pinoox\Component\Database\Model`** extend करती हैं। यह Pinoox का base class है: automatic app connection और table prefix handling के साथ Eloquent wrap करता है।

---

## Model बनाएँ

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

Physical table name `DB::tableNameForModel()` के ज़रिए resolve होता है — app prefix automatically apply होता है।

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

Model automatically namespace से app connection pick करता है:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

Manual queries के लिए:

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

## Model पर transaction

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Tips

- Models app `Model/` folder में — pincore में नहीं (जब तक framework fork न करें)।
- `$fillable` या `$guarded` define करें।
- Core tables के लिए `Pinoox\Model\UserModel` और अन्य pincore models उपयोग करें।

---

## संबंधित docs

- [Database getting started](../database/getting-started.md)
- [Relationships](./relationships.md)
- [Migrations](../database/migrations.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
