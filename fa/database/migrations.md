# Migrations

[← بازگشت به فهرست](../README.md)

Migrationها تغییرات **schema** دیتابیس را نسخه‌بندی می‌کنند. در پینوکس 3.x فایل‌های اپ در `apps/{package}/database/migrations/` و core در `pincore/database/migrations/` (`~pincore/database/migrations`) قرار دارند.

---

## ساخت migration

```bash
php pinoox migrate:create posts com_acme_blog
php pinoox migrate:create CreatePosts com_acme_blog
php pinoox migrate:create create_posts_table com_acme_blog
```

هر سه این فایل را می‌سازند:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

alias: `mg:create`، `mg:make`، `make:migration`.

### نام‌گذاری

stub از روی نام، یا از `--create` / `--table` انتخاب می‌شود:

| ورودی | فایل | Stub |
|-------|------|------|
| `posts` / `CreatePosts` / `create_posts_table` | `create_posts_table.php` | `$this->schema->create()` |
| `add_email_to_users` | `add_email_to_users.php` | `$this->schema->table()` |
| `drop_posts_table` | `drop_posts_table.php` | `$this->schema->dropIfExists()` |
| `sync_legacy_flags --table=users` | `sync_legacy_flags.php` | `$this->schema->table()` |
| `add_status --create=orders` | `add_status.php` | `$this->schema->create()` |

```bash
php pinoox migrate:create add_email_to_users com_acme_blog
php pinoox migrate:create drop_posts_table com_acme_blog
php pinoox migrate:create sync_legacy_flags com_acme_blog --table=users
php pinoox make:migration add_status --create=orders com_acme_blog
```

`migrate:drop` **جداول را واقعاً حذف می‌کند** و تاریخچه را پاک می‌کند. برای ساخت فایل DROP/ALTER از `migrate:create drop_*_table` (یا `add_*` / `--table=`) استفاده کنید.

---

## ساختار فایل

```php
<?php
namespace App\com_acme_blog\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create('posts', function (Blueprint $table) {
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
        $this->schema->dropIfExists('posts');
    }
};
```

پکیج فعلی خودش تشخیص داده می‌شود. این دو معادل‌اند:

```php
$this->schema->create('posts', function (Blueprint $table) { /* ... */ });
$this->schema->create($this->table('posts'), function (Blueprint $table) { /* ... */ });
```

آرگومان پکیج را فقط وقتی بگذارید که این migration جدول **اپ دیگری** را هدف بگیرد:

```php
$this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
    // ...
});
```

---

## اجرا

```bash
# migration اپ
php pinoox migrate com_acme_blog

# migration پلتفرم / هسته (جداول pinx_*)
php pinoox migrate platform
```

---

## وضعیت و rollback

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## migration core (مثال)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create(Table::USER, function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

جداول core: prefix **`pinx_`**.

---

## namespace

| محل | Namespace |
|-----|-----------|
| اپ | `App\{package}\database\migrations` |
| core | `Pinoox\Database\migrations` |

---

## مسیر legacy

فایل‌های جدید و موجود فقط از `database/migrations/` خوانده می‌شوند. پوشهٔ قدیمی `apps/{package}/migrations/` **اسکن نمی‌شود**.

---

## تفاوت Migration / Seed / Patch

| نوع | کاربرد | دستور |
|-----|--------|-------|
| Migration | schema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | داده اولیه (دستی / فراخوانی صریح) | `php pinoox seeder:run {package}` یا `$this->seed()` |
| Patch | تغییر یک‌باره داده | `php pinoox patch:run {package}` |

نصب اپ فقط migration و patch را اجرا می‌کند — **seeder خودکار نیست**. اگر موقع نصب به seed نیاز دارید، از داخل migration (یا patch) با `$this->seed('Name')` یا `$this->seedAll()` صدا بزنید. جزئیات: [داده آزمایشی — Seeder](../eloquent-orm/factories.md#فراخوانی-از-کد-migration--patch--portal).

راهنمای کامل Patch: [Patch (به‌روزرسانی داده)](../advanced/patches.md).

---

## بهترین شیوه‌ها

- هر migration یک تغییر منطقی (یک جدول یا یک ALTER).
- `down()` را همیشه بنویسید.
- migration اجراشده را edit نکنید — migration جدید بسازید.
- foreign key به پکیج دیگر (مثلاً core از داخل اپ): `$this->table(Table::FILE, 'platform')`.

---

## مستندات مرتبط

- [شروع کار با دیتابیس](./getting-started.md)
- [Seeder / Factory — داده آزمایشی](../eloquent-orm/factories.md)
- [پیکربندی DB اپ (app.php)](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)
