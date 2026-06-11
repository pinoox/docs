# Eloquent ORM — начало работы

[← Вернуться к оглавлению](../README.md)

Модели приложений находятся в **`apps/{package}/Model/`** и наследуют **`Pinoox\Component\Database\Model`**. Это базовый класс Pinoox: он оборачивает Eloquent с автоматическим подключением приложения и обработкой префикса таблиц.

---

## Создание модели

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

Физическое имя таблицы разрешается через `DB::tableNameForModel()` — префикс приложения применяется автоматически.

---

## Константы таблиц

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

## Базовый CRUD

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

## Query scopes (цепочка)

```php
$posts = PostModel::query()
    ->where('status', 'published')
    ->orderByDesc('created_at')
    ->limit(10)
    ->get();
```

---

## Подключение к базе данных

Модель автоматически выбирает подключение приложения из своего namespace:

```php
public function getConnectionName()
{
    return parent::getConnectionName() ?? DB::connectionNameForModel(static::class);
}
```

Для ручных запросов:

```php
DB::app('com_acme_blog')->table('posts')->get();
```

---

## Префикс таблиц — напоминание

| Сценарий | Таблица `posts` |
|----------|---------------|
| Общая БД, `com_acme_blog` | `blog_posts` (префикс из пакета) |
| Выделенная БД, пустой префикс | `posts` |
| Явный префикс `shop_` | `shop_posts` |
| Ядро | `pincore_user` и т.д. |

---

## Транзакция на модели

```php
$post->transaction(function () use ($post) {
    $post->update(['status' => 'published']);
    // ...
});
```

---

## Советы

- Модели принадлежат папке `Model/` приложения — не pincore (если вы не форкаете фреймворк).
- Определите `$fillable` или `$guarded`.
- Для таблиц ядра используйте `Pinoox\Model\UserModel` и другие модели pincore.

---

## Связанные документы

- [Начало работы с базой данных](../database/getting-started.md)
- [Relationships](./relationships.md)
- [Миграции](../database/migrations.md)

---

[← Вернуться к оглавлению](../README.md)
