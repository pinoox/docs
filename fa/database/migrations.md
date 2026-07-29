# Migrations

[← بازگشت به فهرست](../README.md)

Migrationها تغییرات **schema** دیتابیس را نسخه‌بندی می‌کنند. در پینوکس 3.x فایل‌های اپ در `apps/{package}/database/migrations/` و core در `system/database/migrations/` قرار دارند.

---

## ساخت migration

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

خروجی:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

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

`$this->table('posts', $package)` prefix صحیح اپ را اعمال می‌کند.

---

## اجرا

```bash
# migration اپ
php pinoox migrate com_acme_blog

# migration هسته
php pinoox migrate pincore

# migration پلتفرم (جداول pinx_*)
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

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

جداول core: prefix **`pincore_`** (یا `pinx_` برای scope platform).

---

## namespace

| محل | Namespace |
|-----|-----------|
| اپ | `App\{package}\database\migrations` |
| core | `Pinoox\Database\migrations` |

---

## مسیر legacy

پینوکس هنوز `apps/{package}/migrations/` قدیمی را می‌خواند، اما فایل **جدید** در `database/migrations/` ساخته می‌شود.

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
- foreign key به جداول core با `$this->table(Table::FILE, 'platform')`.

---

## مستندات مرتبط

- [شروع کار با دیتابیس](./getting-started.md)
- [Seeder / Factory — داده آزمایشی](../eloquent-orm/factories.md)
- [پیکربندی DB اپ (app.php)](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)
