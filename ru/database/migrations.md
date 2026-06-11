# Миграции

[← Вернуться к оглавлению](../README.md)

Миграции версионируют изменения **схемы** базы данных. В Pinoox 3.x файлы приложений находятся в `apps/{package}/database/migrations/`, а файлы ядра — в `system/database/migrations/`.

---

## Создание миграции

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Результат:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## Структура файла

```php
<?php
namespace App\com_acme_blog\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('posts', 'com_acme_blog'));
    }
};
```

`$this->table('posts', $package)` применяет правильный префикс приложения.

---

## Запуск миграций

```bash
# миграция приложения
php pinoox migrate com_acme_blog

# миграция ядра
php pinoox migrate pincore

# миграция платформы (таблицы pinx_*)
php pinoox migrate platform
```

---

## Статус и откат

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Миграция ядра (пример)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Таблицы ядра: префикс **`pincore_`** (или `pinx_` для scope platform).

---

## Пространства имён

| Расположение | Пространство имён |
|----------|-----------|
| Приложение | `App\{package}\database\migrations` |
| Ядро | `Pinoox\Database\migrations` |

---

## Устаревший путь

Pinoox по-прежнему читает старую папку `apps/{package}/migrations/`, но **новые** файлы создаются в `database/migrations/`.

---

## Migration vs Seed vs Patch

| Тип | Назначение | Команда |
|------|---------|---------|
| Migration | Схема (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Начальные данные | `php pinoox seeder:run {package}` |
| Patch | Одноразовое изменение данных | `php pinoox patch:run {package}` |

Полное руководство по патчам: [Патчи (обновление данных)](./patches.md).

---

## Лучшие практики

- Одно логическое изменение на миграцию (одна таблица или один ALTER).
- Всегда пишите `down()`.
- Не редактируйте миграцию, которая уже выполнена — создайте новую.
- Внешние ключи к таблицам ядра используют `$this->table(Table::FILE, 'platform')`.

---

## Связанные документы

- [Начало работы с базой данных](./getting-started.md)
- [Сиды / factories](../eloquent-orm/factories.md)
- [Конфигурация БД приложения (app.php)](../start/app-manifest.md)

---

[← Вернуться к оглавлению](../README.md)
