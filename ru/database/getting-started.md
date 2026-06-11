# Начало работы с базой данных

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x предоставляет слой базы данных через **Illuminate Database** (Eloquent + Query Builder) и Portal **`Pinoox\Portal\Database\DB`**. Каждое приложение определяет подключение в `app.php`; учётные данные платформы хранятся в `.env` проекта.

---

## Portal DB

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // подключение активного приложения
DB::app('com_my_shop')->table('orders')->get();      // подключение конкретного приложения
DB::core()->table('user')->get();                     // таблицы pincore
DB::tableName('orders');                             // физическое имя с префиксом
```

---

## Платформа по умолчанию

```php
// app.php
'database' => null,
```

Модели и запросы используют подключение по умолчанию проекта (`DB_CONNECTION` в `.env`).

---

## Именованное подключение платформы

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox клонирует подключение с именем `app_{package}_default` из блока платформы.

---

## Префикс таблиц

### Приложение на общей БД (без выделенной базы)

По умолчанию: короткий префикс из имени пакета.

```php
'database' => null,
// com_pinoox_manager + таблица notifications → manager_notifications
```

### Явный префикс

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// или
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### Выделенная БД — без префикса

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Таблицы ядра

Всегда префикс **`pincore_`**: `pincore_user`, `pincore_token`, `pincore_file`.

---

## Полностью выделенная база данных

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

Несколько подключений:

```php
'database' => [
    'default' => 'primary',
    'connections' => [
        'primary' => ['driver' => 'sqlite', 'database' => ':memory:'],
        'analytics' => ['use' => 'mysql', 'prefix' => 'an_'],
    ],
],
```

```php
DB::app('com_my_shop', 'analytics')->table('events')->get();
```

---

## Ключи .env приложения

| Ключ | Соответствует |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | выделенные учётные данные |

**Не** используйте `DB_CONNECTION` в `.env` приложения — он игнорируется.

---

## Структура папки database

```text
apps/{package}/
├── patches/                 ← одноразовые патчи данных
└── database/
    migrations/
    seed/
```

---

## Разрешение имён таблиц

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Советы

- Бизнес-логику держите в Model/Component; контроллеры остаются тонкими.
- Миграции и сиды живут только в папке `database/` приложения — не в pincore.
- Pinker может переопределять `database.use` и `database.prefix`.

---

## Связанные документы

- [Query Builder](./query-builder.md)
- [Миграции](./migrations.md)
- [Eloquent — начало работы](../eloquent-orm/getting-started.md)
- [Конфигурация БД приложения (app.php)](../start/app-manifest.md)

---

[← Вернуться к оглавлению](../README.md)
