# شروع کار با دیتابیس

[← بازگشت به فهرست](../README.md)

پینوکس 3.x لایه دیتابیس را با **Illuminate Database** (Eloquent + Query Builder) و Portal **`Pinoox\Portal\Database\DB`** در اختیار اپ‌ها قرار می‌دهد. هر اپ اتصال خود را در `app.php` تعریف می‌کند؛ credential پلتفرم در `.env` پروژه است.

---

## Portal DB

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // اتصال اپ فعال
DB::app('com_my_shop')->table('orders')->get();      // اتصال اپ مشخص
DB::core()->table('user')->get();                     // جداول pincore
DB::tableName('orders');                             // نام فیزیکی با prefix
```

---

## پیش‌فرض پلتفرم

```php
// app.php
'database' => null,
```

مدل‌ها و Queryها از connection پیش‌فرض پروژه (`DB_CONNECTION` در `.env`) استفاده می‌کنند.

---

## اتصال نام‌گذاری‌شده پلتفرم

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

پینوکس connection با نام `app_{package}_default` از بلوک platform clone می‌کند.

---

## Prefix جدول

### اپ روی DB مشترک (بدون DB اختصاصی)

پیش‌فرض: prefix کوتاه از نام package.

```php
'database' => null,
// com_pinoox_manager + جدول notifications → manager_notifications
```

### Prefix صریح

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// یا
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### DB اختصاصی — بدون prefix

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### جداول core

همیشه prefix **`pincore_`**: `pincore_user`, `pincore_token`, `pincore_file`.

---

## DB اختصاصی کامل

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

چند connection:

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

## کلیدهای .env اپ

| کلید | نگاشت |
|------|--------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | credential اختصاصی |

از `DB_CONNECTION` در `.env` اپ استفاده **نکنید** — نادیده گرفته می‌شود.

---

## ساختار پوشه database

```text
apps/{package}/
├── patches/                 ← Patch (داده یک‌باره)
└── database/
    migrations/
    seed/
```

---

## resolve نام جدول

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## نکات

- منطق business در Model/Component؛ کنترلر نازک بماند.
- migration و seed فقط در `database/` اپ — نه در pincore.
- Pinker می‌تواند `database.use` و `database.prefix` را override کند.

---

## CLI (`db:*`)

| دستور | کاربرد |
|--------|--------|
| `db:list` | اتصال‌های پلتفرم یا خلاصه اپ (`--all`, `--test`) |
| `db:show {target}` | جزئیات + وضعیت برای `platform`، connection، یا package |
| `db:test` | تست اتصال |
| `db:create {name}` | افزودن profile پلتفرم |
| `db:update {target}` | به‌روزرسانی host، credential، prefix |
| `db:prefix {package} {prefix}` | فقط prefix جدول اپ |

```bash
php pinoox db:list --test
php pinoox db:show com_my_shop --json
php pinoox db:prefix com_my_shop shop_
```

اگر `.env` کلید `DB_*` داشته باشد، runtime با **env-over-pinker** ممکن است Pinker را override کند.

مرجع: [CLI](../start/cli-reference.md)، [Pinker](../advanced/pinker.md).

---

## مستندات مرتبط

- [Query Builder — سازنده کوئری](./query-builder.md)
- [Migration — مهاجرت](./migrations.md)
- [Eloquent — شروع به کار](../eloquent-orm/getting-started.md)
- [پیکربندی DB اپ (app.php)](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)
