# Migrations

[← بازگشت به فهرست](../README.md)

Migration تغییرات **schema** را نسخه‌بندی می‌کند: ساخت جدول، افزودن ستون، حذف ایندکس، و هم‌تراز ماندن دیتابیس هر اپ. هر فایل دو متد دارد: `up()` (اعمال) و `down()` (برگشت).

| | اپ | پلتفرم / هسته |
|--|-----|----------------|
| مسیر | `apps/{package}/database/migrations/` | `pincore/database/migrations/` (`~pincore/…`) |
| Namespace | `App\{package}\database\migrations` | `Pinoox\Database\migrations` |
| Prefix | از `app.php` / `DB_PREFIX` | `pinx_` |
| اجرا | `php pinoox migrate {package}` | `php pinoox migrate platform` |

فقط `database/migrations/` خوانده می‌شود. پوشهٔ قدیمی `apps/{package}/migrations/` اسکن نمی‌شود.

**تک‌اپ (Pinx):** فایل‌ها در ریشهٔ پروژه (`database/migrations/`) هستند. از `pinx migrate…` استفاده کنید — آرگومان پکیج لازم نیست.

---

## شروع سریع

```bash
# پلتفرم چنداپ
php pinoox migrate:create posts com_acme_blog
php pinoox migrate com_acme_blog

# Pinx تک‌اپ
pinx migrate:create posts
pinx migrate
```

فایل ساخته‌شده را باز کنید، ستون‌ها را بنویسید، بعد migrate را اجرا کنید. تاریخچه در جدول `history` پلتفرم (`pinx_history`) به‌ازای هر پکیج ذخیره می‌شود.

alias ساخت: `mg:create`، `mg:make`، `make:migration`.

---

## ساختار فایل

فایل‌های ساخته‌شده کلاس ناشناس هستند و از `MigrationBase` ارث می‌برند:

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
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists('posts');
    }
};
```

`$this->schema` از قبل به اتصال **پکیج فعلی** وصل است. نام منطقی `'posts'` کافی است — prefix خودکار اعمال می‌شود.

این دو معادل‌اند:

```php
$this->schema->create('posts', function (Blueprint $table) { /* ... */ });
$this->schema->create($this->table('posts'), function (Blueprint $table) { /* ... */ });
```

آرگومان پکیج را فقط وقتی بگذارید که این فایل جدول **اپ دیگری** را هدف بگیرد:

```php
$this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
    // ...
});
```

`down()` را همیشه بنویسید تا rollback بتواند تغییر را برگرداند.

---

## ساخت migration (نام‌گذاری)

**نام فایل** نوع stub را مشخص می‌کند. با `--create=` / `--table=` هم می‌توانید اجبار کنید.

| ورودی | نام فایل | Stub |
|-------|----------|------|
| `posts` / `CreatePosts` / `create_posts_table` | `create_posts_table` | ساخت جدول |
| `add_email_to_users` | `add_email_to_users` | تغییر جدول |
| `drop_posts_table` | `drop_posts_table` | حذف جدول |
| `sync_legacy_flags --table=users` | `sync_legacy_flags` | alter روی `users` |
| `add_status --create=orders` | `add_status` | ساخت `orders` |
| `fix_legacy_flags` (بدون فعل شناخته‌شده) | `fix_legacy_flags` | `up`/`down` خالی |

```bash
php pinoox migrate:create posts com_acme_blog
php pinoox migrate:create add_email_to_users com_acme_blog
php pinoox migrate:create drop_posts_table com_acme_blog
php pinoox migrate:create sync_legacy_flags com_acme_blog --table=users
php pinoox make:migration add_status --create=orders com_acme_blog

pinx migrate:create add_email_to_users
pinx make migration sync_legacy_flags --table=users
```

نمونه مسیر خروجی:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

`migrate:drop` **جداول را واقعاً حذف می‌کند** و تاریخچه را پاک می‌کند. برای *ساخت فایل* DROP/ALTER از `migrate:create drop_*_table` (یا `add_*` / `--table=`) استفاده کنید.

---

## مثال‌ها

### ۱) جدول جدید

```bash
php pinoox migrate:create create_comments_table com_acme_blog
```

```php
public function up()
{
    $this->schema->create('comments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('post_id');
        $table->string('author', 120);
        $table->text('body');
        $table->boolean('approved')->default(false);
        $table->timestamps();

        $table->index('post_id');
    });
}

public function down(): void
{
    $this->schema->dropIfExists('comments');
}
```

کمک‌های پرکاربرد ستون: `id()`، `string()`، `text()`، `integer()`، `unsignedInteger()`، `boolean()`، `json()`، `timestamp()`، `timestamps()`، `softDeletes()`، `index()`، `unique()`.

### ۲) افزودن / تغییر ستون

```bash
php pinoox migrate:create add_email_to_users com_acme_blog
```

```php
public function up()
{
    $this->schema->table('users', function (Blueprint $table) {
        $table->string('email', 190)->nullable()->after('name');
        $table->unique('email');
    });
}

public function down(): void
{
    $this->schema->table('users', function (Blueprint $table) {
        $table->dropUnique(['email']);
        $table->dropColumn('email');
    });
}
```

`after()` روی MySQL/MariaDB کار می‌کند. در `down()` حتماً برعکس همان تغییر را بنویسید.

### ۳) حذف جدول

```bash
php pinoox migrate:create drop_legacy_logs_table com_acme_blog
```

```php
public function up()
{
    $this->schema->dropIfExists('legacy_logs');
}

public function down(): void
{
    $this->schema->create('legacy_logs', function (Blueprint $table) {
        $table->id();
        $table->text('message')->nullable();
        $table->timestamps();
    });
}
```

### ۴) کلید خارجی به هسته (`user`، `file`، …)

از داخل migration **اپ**، جداول پلتفرم را با `[name, 'platform']` ارجاع دهید:

```php
use Pinoox\Model\Table;

public function up()
{
    $this->schema->create('posts', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('user_id')->nullable();
        $table->unsignedInteger('cover_id')->nullable();
        $table->string('title');
        $table->timestamps();

        $table->foreign('user_id')
            ->references('user_id')
            ->on([Table::USER, 'platform'])
            ->nullOnDelete();

        $table->foreign('cover_id')
            ->references('file_id')
            ->on([Table::FILE, 'platform'])
            ->nullOnDelete();
    });
}
```

`$this->foreignTable('user', 'platform')` نام فیزیکی خام را وقتی خارج از `->on()` لازم دارید برمی‌گرداند.

### ۵) seed هنگام ساخت جدول

نصب اپ فقط **migration و patch** را اجرا می‌کند، نه seeder. اگر اپ باید با داده بیاید، از داخل `up()` صدا بزنید:

```php
public function up()
{
    $this->schema->create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    $this->seed('CategorySeeder');   // یک فایل
    // $this->seedAll();             // همه seederهای پکیج
}
```

جزئیات: [Seeder / Factory](../eloquent-orm/factories.md#فراخوانی-از-کد-migration--patch--portal).

---

## اجرا، وضعیت، rollback

```bash
php pinoox migrate com_acme_blog
php pinoox migrate platform          # جداول هسته pinx_* (قبل از migrate اپ هم اجرا می‌شود)
php pinoox migrate --devdb           # DevDB محلی
php pinoox migrate --ignore-fk       # MySQL/MariaDB: غیرفعال کردن FK برای همین اجرا
php pinoox migrate --force           # حتی اگر بعضی جداول از قبل وجود داشته باشند

php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog          # آخرین batch
php pinoox migrate:rollback com_acme_blog --step=2
php pinoox migrate:rollback com_acme_blog --all
```

هر **فایل** موفق یک batch است؛ پس `--step=1` آخرین فایل را برمی‌گرداند.

| دستور | کار |
|-------|-----|
| `migrate` | اجرای `up()`های مانده |
| `migrate:status` | انجام‌شده در برابر مانده |
| `migrate:rollback` | فراخوانی `down()` (N batch آخر) |
| `migrate:reset` | `down()` همه batchها |
| `migrate --refresh` | برگشت همه با `down()`، بعد دوباره migrate |
| `migrate:fresh` / `migrate --fresh` | **حذف جداول**، پاک کردن تاریخچه، migrate |
| `migrate:drop` | **حذف جداول** و پاک کردن تاریخچه (بدون اجرای دوباره) |

`fresh` / `drop` متد `down()` را صدا نمی‌زنند و جداول را از روی نام فایل‌ها hard-drop می‌کنند. خود جدول history پلتفرم حذف نمی‌شود. بدون `--force` تأیید می‌خواهند.

Pinx (فقط اپ فعلی):

```bash
pinx migrate
pinx migrate --platform              # اول platform بعد اپ
pinx migrate:st
pinx migrate:rb --step=1
pinx migrate --fresh
pinx migrate:drop --force
```

---

## پلتفرم / هسته

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;
use Illuminate\Database\Schema\Blueprint;

$this->schema->create(Table::USER, function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

نام‌های منطقی هسته: `Table::USER`، `FILE`، `TOKEN`، `HISTORY`، `ROLE`، `PERMISSION`، … prefix فیزیکی: **`pinx_`**.

migrate یک اپ همیشه اول **platform** را اجرا می‌کند تا جداول هسته قبل از FK اپ موجود باشند.

---

## کمک‌های `$this->schema`

| متد | کاربرد |
|-----|--------|
| `create($name, fn)` | جدول جدید |
| `table($name, fn)` | تغییر جدول موجود |
| `drop` / `dropIfExists` | حذف جدول |
| `hasTable` / `hasColumn` | شرط داخل `up()` |
| `disableForeignKeyConstraints()` | DDL گروهی (بعدش دوباره فعال کنید) |

داخل callback بلوپرینت: نوع‌ها (`string`، `text`، `integer`، `json`، …)، default، `nullable()`، `unique()`، `index()`، `foreign()`، `timestamps()`، `softDeletes()`.

---

## تفاوت Migration / Seed / Patch

| نوع | کاربرد | دستور |
|-----|--------|-------|
| Migration | schema (CREATE/ALTER) | `php pinoox migrate {package}` / `pinx migrate` |
| Seeder | داده اولیه / نمونه | `php pinoox seeder:run` یا `$this->seed()` |
| Patch | اصلاح یک‌باره **داده** (نه schema) | `php pinoox patch:run {package}` |

نصب اپ migration و patch را اجرا می‌کند — **seeder خودکار نیست**. راهنمای Patch: [Patch](../advanced/patches.md).

---

## نکات

- هر فایل یک تغییر منطقی (یک جدول یا یک ALTER).
- migration اجراشده روی هیچ محیطی را edit نکنید — فایل جدید بسازید.
- `down()` را دقیق نگه دارید؛ وگرنه `rollback` / `reset` / `--refresh` می‌شکنند.
- `dropIfExists` و FK از نوع `nullable` بهتر از فرض دیتابیس خالی است.
- DevDB محلی: [DevDB](../start/devdb.md) و `php pinoox migrate --devdb` / `pinx migrate`.

---

## مستندات مرتبط

- [شروع کار با دیتابیس](./getting-started.md)
- [Seeder / Factory](../eloquent-orm/factories.md)
- [پیکربندی DB اپ (`app.php`)](../start/app-manifest.md)
- [Pinx CLI](../start/pinx-cli.md)
- [مرجع CLI پینوکس](../start/cli-reference.md)

---

[← بازگشت به فهرست](../README.md)
