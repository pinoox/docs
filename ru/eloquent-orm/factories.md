# Тестовые данные — Seeders

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x не включает **Model Factory** (в стиле Laravel) в CLI. Рекомендуемый подход для начальных и dev-данных — **Seeders** с `SeederBase` в `apps/{package}/database/seeders/`.

---

## Создание seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## Структура

```php
<?php
namespace App\com_acme_blog\database\seeders;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Seeder\SeederBase;
use Pinoox\Portal\Hash;

return new class extends SeederBase
{
    public function run(): void
    {
        PostModel::insert([
            [
                'user_id' => 1,
                'title' => 'First post',
                'body' => 'Sample content',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Second post',
                'body' => '...',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
```

---

## Вызов другого seeder

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
    ]);

    // зависимые данные после пользователей
    PostModel::factory(); // ❌ недоступно — используйте insert или create вручную
}
```

---

## create с Model

```php
for ($i = 1; $i <= 20; $i++) {
    PostModel::create([
        'user_id' => 1,
        'title' => "Post #{$i}",
        'body' => 'Lorem ipsum',
        'status' => $i % 2 ? 'published' : 'draft',
    ]);
}
```

---

## Запуск seeders

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## Рекомендуемый порядок

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeders в production

- Только **необходимые** данные (роли, настройки по умолчанию).
- Защищайте fake/dev-данные через `APP_ENV`:

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // sample data
}
```

---

## Seeder vs Patch

| Seeder | Patch |
|--------|-------|
| Начальные / примерные данные | Одноразовое исправление существующих данных |
| `seeder:run` — повторяемо с осторожностью | `patch:run` — отслеживается один раз |

---

## Советы

- Пишите идемпотентные seeders (`firstOrCreate` вместо слепого `insert`).
- Не коммитьте реальные учётные данные в seeders.
- Для unit-тестов используйте фикстуры Pest или sqlite `:memory:`.

---

## Связанные документы

- [Миграции](../database/migrations.md)
- [Eloquent — начало работы](./getting-started.md)
- [Конфигурация БД приложения (app.php)](../start/app-manifest.md)

---

[← Вернуться к оглавлению](../README.md)
