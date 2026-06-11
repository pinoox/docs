# Eloquent ORM 入门

[← 返回索引](../README.md)

应用模型位于 **`apps/{package}/Model/`**，并继承 **`Pinoox\Component\Database\Model`**。这是 Pinoox 的基类：在 Eloquent 之上封装了自动应用连接和表前缀处理。

---

## 创建模型

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

物理表名通过 `DB::tableNameForModel()` 解析 — 应用前缀会自动应用。

---

## 表常量

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

## 基本 CRUD

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

## 查询作用域（可链式调用）

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## 数据库连接

模型会根据其命名空间自动选择应用连接：

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

手动查询：

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## 表前缀 — 提醒

| 场景 | 表 `posts` |
|----------|---------------|
| 共享数据库，`com_acme_blog` | `blog_posts`（来自包的前缀） |
| 独立数据库，空前缀 | `posts` |
| 显式前缀 `shop_` | `shop_posts` |
| 核心 | `pincore_user` 等 |

---

## 模型上的事务

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## 提示

- 模型放在应用的 `Model/` 文件夹 — 不在 pincore 中（除非你 fork 框架）。
- 定义 `$fillable` 或 `$guarded`。
- 核心表使用 `Pinoox\Model\UserModel` 及其他 pincore 模型。

---

## 相关文档

- [数据库入门](../database/getting-started.md)
- [关联关系](./relationships.md)
- [迁移](../database/migrations.md)

---

[← 返回索引](../README.md)
