# Патчи (обновление данных)

[← Вернуться к оглавлению](../README.md)

**Патч** в Pinoox 3.x — это **одноразовое операционное изменение**: исправление данных, перенос записей, синхронизация конфигурации или логика после обновления. Это не **миграция** (схема) и не **seeder** (повторяемые начальные данные).

---

## Когда использовать патч

| Инструмент | Назначение |
|------|---------|
| **Migration** | CREATE/ALTER таблиц и столбцов |
| **Seeder** | Начальные или примерные данные (ручные запуски) |
| **Patch** | Выполнить один раз и отследить в `history` |

Примеры патчей:

- Исправление невалидных строк после бага
- Заполнение значений по умолчанию для старых записей
- Переименование значений конфигурации в БД
- Логика после обновления нового релиза

---

## Расположение файлов

```text
vendor/pinoox/pincore/patches/     ← платформа (CLI: platform)
apps/{package}/patches/            ← ваше приложение
```

> Устаревший путь `database/patches/` **не используется**. Патчи живут рядом с `app.php`, а не в `database/`.

---

## Создание патча

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

CLI создаёт файл с меткой времени, например:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

Форма заготовки (анонимный класс):

```php
<?php
namespace App\com_acme_shop\patches;

use Pinoox\Component\Database\Patch\PatchBase;
use Pinoox\Portal\Database\DB;

return new class extends PatchBase
{
    public function description(): string
    {
        return 'Set empty contact status to active';
    }

    public function shouldRun(): bool
    {
        return DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->exists();
    }

    public function canRollback(): bool
    {
        return false;
    }

    public function up(): void
    {
        DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->update(['status' => 'active']);
    }
}
```

Пространство имён платформы: `Pinoox\Patches`.

---

## Методы PatchBase

| Метод | Роль |
|--------|------|
| `up()` | Основная логика (вызывается через `run()`) |
| `down()` | Откат, когда `canRollback()` возвращает true |
| `shouldRun()` | Если false, патч записывается как **skipped** |
| `canRollback()` | Разрешён ли откат |
| `description()` | Человекочитаемый текст в history |
| `metadata()` | Дополнительный JSON в history |

---

## CLI-команды

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Примечание:** `patch:run` сначала выполняет **миграции платформы**, затем патчи выбранного пакета.

Алиас: `php pinoox patch` = `patch:run`.

---

## Таблица history

Миграции и патчи используют общую таблицу **`history`**:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Успешные патчи не выполняются повторно автоматически.

---

## Установщик

Системное приложение `com_pinoox_installer` запускает миграции и патчи во время установки через `SetupService`.

---

## Лучшие практики

- Не редактируйте патч, который уже выполнен — создайте новый.
- Для схемы используйте миграции, а не патчи.
- Реализуйте `shouldRun()`, чтобы идемпотентные проверки пропускали лишнюю работу.
- Включайте откат только когда `down()` безопасен.

---

## Связанные документы

- [Миграции](./migrations.md)
- [Сиды / factories](../eloquent-orm/factories.md)
- [Справочник CLI](../start/cli-reference.md)

---

[← Вернуться к оглавлению](../README.md)
