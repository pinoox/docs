# Database Getting Started

[← Back to index](../README.md)

Pinoox 3.x provides the database layer through **Illuminate Database** (Eloquent + Query Builder) and the **`Pinoox\Portal\Database\DB`** portal. Each app defines its connection in `app.php`; platform credentials live in the project `.env`.

---

## DB portal

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // active app connection
DB::app('com_my_shop')->table('orders')->get();      // specific app connection
DB::core()->table('user')->get();                     // pincore tables
DB::tableName('orders');                             // physical name with prefix
```

---

## Platform default

```php
// app.php
'database' => null,
```

Models and queries use the project default connection (`DB_CONNECTION` in `.env`).

---

## Named platform connection

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox clones a connection named `app_{package}_default` from the platform block.

---

## Table prefix

### App on a shared DB (no dedicated database)

Default: short prefix derived from the package name.

```php
'database' => null,
// com_pinoox_manager + table notifications → manager_notifications
```

### Explicit prefix

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// or
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### Dedicated DB — no prefix

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Core tables

Always prefix **`pincore_`**: `pincore_user`, `pincore_token`, `pincore_file`.

---

## Full dedicated database

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

Multiple connections:

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

## App .env keys

| Key | Maps to |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | dedicated credentials |

Do **not** use `DB_CONNECTION` in the app `.env` — it is ignored.

---

## database folder layout

```text
apps/{package}/
├── patches/                 ← one-time data patches
└── database/
    migrations/
    seed/
```

---

## Resolve table names

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Tips

- Keep business logic in Model/Component; controllers stay thin.
- Migrations and seeds live only in the app `database/` folder — not in pincore.
- Pinker can override `database.use` and `database.prefix`.

---

## Related docs

- [Query Builder](./query-builder.md)
- [Migrations](./migrations.md)
- [Eloquent — getting started](../eloquent-orm/getting-started.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← Back to index](../README.md)
