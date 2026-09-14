# Patch (به‌روزرسانی داده)

[← بازگشت به فهرست](../README.md)

**Patch** در پینوکس ۳.x یک **تغییر عملیاتی یک‌باره** است: اصلاح داده، انتقال رکورد، همگام‌سازی تنظیمات، یا منطق ارتقا بعد از آپدیت. با **Migration** (تغییر schema) و **Seeder** (داده اولیه تکرارپذیر) فرق دارد.

---

## چه موقع Patch؟

| ابزار | کاربرد |
|-------|--------|
| **Migration** | ساخت/تغییر جدول و ستون |
| **Seeder** | داده نمونه یا اولیه (دستی / فراخوانی صریح؛ نه موقع نصب) |
| **Patch** | یک‌بار اجرا شود و در `history` ثبت شود |
| **lifecycle.php** | هر نصب/آپدیت/حذف/ریست (سید، پوشه، پاکسازی فایل) — [boot و رویدادها](./boot-and-events.md) |

مثال‌های Patch:

- اصلاح داده‌های نامعتبر بعد از باگ
- پر کردن مقدار پیش‌فرض برای رکوردهای قدیمی
- rename مقدار در ستون config
- منطق post-update بعد از نصب نسخه جدید

---

## محل فایل‌ها

```text
vendor/pinoox/pincore/patches/     ← پلتفرم (CLI: platform)
apps/{package}/patches/            ← اپ شما
```

> مسیر قدیمی `database/patches/` **استفاده نمی‌شود**. Patch کنار `app.php` است، نه داخل `database/`.

---

## ساخت Patch

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

فایل با timestamp ساخته می‌شود، مثلاً:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

الگوی stub (کلاس ناشناس):

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

Namespace پلتفرم: `Pinoox\Patches`.

---

## متدهای PatchBase

| متد | نقش |
|-----|-----|
| `up()` | منطق اصلی (از `run()` صدا زده می‌شود) |
| `down()` | برگشت — فقط اگر `canRollback()` true باشد |
| `shouldRun()` | اگر `false` باشد، patch **skipped** ثبت می‌شود |
| `canRollback()` | آیا rollback مجاز است |
| `description()` | توضیح انسانی در history |
| `metadata()` | داده اضافه JSON در history |
| `seed($name, $package?)` | اجرای seeder با نام فایل |
| `seedAll($package?)` | اجرای همهٔ seederهای یک پکیج |

```php
public function up(): void
{
    $this->seed('GatewaySeeder');
}
```

جزئیات: [فراخوانی از کد](../eloquent-orm/factories.md#فراخوانی-از-کد-migration--patch--portal).

---

## دستورات CLI

```bash
# اجرای patchهای pending اپ
php pinoox patch:run com_acme_shop

# patchهای پلتفرم
php pinoox patch:run platform

# وضعیت
php pinoox patch:status com_acme_shop

# یک patch مشخص
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status

# rollback (اگر canRollback)
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**نکته:** `patch:run` قبل از patchهای همان package، **migration پلتفرم** (`platform`) را هم اجرا می‌کند تا schema به‌روز باشد.

alias کوتاه: `php pinoox patch` = `patch:run`.

---

## جدول history

Migration و Patch هر دو در جدول **`history`** ثبت می‌شوند:

```text
type = migration | patch | lifecycle
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

هر patch موفق دوباره **اجرا نمی‌شود** مگر با `--force` روی خطا یا تغییر دستی history (توصیه نمی‌شود). `type=lifecycle` اجرای `onInstall` را ثبت می‌کند تا provision دوباره double-seed نکند. Uninstall/reset تاریخچهٔ patch و lifecycle را **پاک** می‌کند تا reinstall دوباره اجرا شود.

---

## نصب اولیه

اپ **installer** (`com_pinoox_installer`) در setup، migrate و patch پروژه را از طریق `SetupService` اجرا می‌کند — patchهای pending پلتفرم و اپ‌ها بعد از نصب اعمال می‌شوند.

---

## بهترین شیوه‌ها

- Patch اجراشده را edit نکنید — patch جدید بسازید.
- برای تغییر schema از migration استفاده کنید، نه patch.
- `shouldRun()` بنویسید تا روی دیتابیس خالی یا قبلاً اصلاح‌شده بی‌اثر بماند.
- rollback فقط وقتی واقعاً reversible است `canRollback(): true` بگذارید.

---

## مستندات مرتبط

- [Migration — مهاجرت](./migrations.md)
- [چرخه عمر پکیج (`lifecycle.php`)](./boot-and-events.md)
- [Seeder / Factory — داده آزمایشی](../eloquent-orm/factories.md)
- [CLI — خط فرمان](../start/cli-reference.md)

---

[← بازگشت به فهرست](../README.md)
