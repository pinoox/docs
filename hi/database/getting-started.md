# Database Getting Started

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x database layer **Illuminate Database** (Eloquent + Query Builder) और **`Pinoox\Portal\Database\DB`** portal के ज़रिए provide करता है। हर app `app.php` में connection define करती है; platform credentials project `.env` में रहते हैं।

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

Models और queries project default connection (`DB_CONNECTION` in `.env`) उपयोग करते हैं।

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

Pinoox platform block से `app_{package}_default` नाम की connection clone करता है।

---

## Table prefix

### Shared DB पर app (dedicated database नहीं)

Default: package name से derived short prefix।

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

हमेशा prefix **`pincore_`**: `pincore_user`, `pincore_token`, `pincore_file`.

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

App `.env` में `DB_CONNECTION` **उपयोग न करें** — ignore होता है।

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

## Table names resolve करें

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Tips

- Business logic Model/Component में रखें; controllers thin रहें।
- Migrations और seeds केवल app `database/` folder में — pincore में नहीं।
- Pinker `database.use` और `database.prefix` override कर सकता है।

---

## संबंधित docs

- [Query Builder](./query-builder.md)
- [Migrations](./migrations.md)
- [Eloquent — getting started](../eloquent-orm/getting-started.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
