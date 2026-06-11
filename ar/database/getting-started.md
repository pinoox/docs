# البدء مع قاعدة البيانات

[← العودة إلى الفهرس](../README.md)

يوفر Pinoox 3.x طبقة قاعدة البيانات عبر **Illuminate Database** (Eloquent + Query Builder) و portal **`Pinoox\Portal\Database\DB`**. يحدّد كل تطبيق اتصاله في `app.php`؛ بيانات اعتماد المنصة في `.env` للمشروع.

---

## portal DB

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // active app connection
DB::app('com_my_shop')->table('orders')->get();      // specific app connection
DB::core()->table('user')->get();                     // pincore tables
DB::tableName('orders');                             // physical name with prefix
```

---

## الافتراضي للمنصة

```php
// app.php
'database' => null,
```

النماذج والاستعلامات تستخدم اتصال المشروع الافتراضي (`DB_CONNECTION` في `.env`).

---

## اتصال منصة مسمّى

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

يستنسخ Pinoox اتصالًا باسم `app_{package}_default` من كتلة المنصة.

---

## بادئة الجدول

### تطبيق على DB مشتركة (بدون قاعدة مخصصة)

الافتراضي: بادئة قصيرة مشتقة من اسم الحزمة.

```php
'database' => null,
// com_pinoox_manager + table notifications → manager_notifications
```

### بادئة صريحة

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

### DB مخصصة — بدون بادئة

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### جداول النواة

البادئة دائمًا **`pincore_`**: `pincore_user`، `pincore_token`، `pincore_file`.

---

## قاعدة بيانات مخصصة كاملة

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

اتصالات متعددة:

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

## مفاتيح .env للتطبيق

| المفتاح | يُربط بـ |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | بيانات اعتماد مخصصة |

**لا** تستخدم `DB_CONNECTION` في `.env` للتطبيق — يُتجاهل.

---

## تخطيط مجلد database

```text
apps/{package}/
├── patches/                 ← one-time data patches
└── database/
    migrations/
    seed/
```

---

## حل أسماء الجداول

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## نصائح

- احتفظ بمنطق الأعمال في Model/Component؛ المتحكمات تبقى رقيقة.
- الترحيلات والبذور تعيش فقط في مجلد `database/` للتطبيق — وليس في pincore.
- Pinker يمكنه تجاوز `database.use` و`database.prefix`.

---

## وثائق ذات صلة

- [Query Builder](./query-builder.md)
- [الترحيلات (Migrations)](./migrations.md)
- [Eloquent — البدء](../eloquent-orm/getting-started.md)
- [إعداد قاعدة بيانات التطبيق (app.php)](../start/app-manifest.md)

---

[← العودة إلى الفهرس](../README.md)
