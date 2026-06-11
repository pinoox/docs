# Patches (تحديثات البيانات)

[← العودة إلى الفهرس](../README.md)

**Patch** في Pinoox 3.x هو **تغيير تشغيلي لمرة واحدة**: إصلاح بيانات، نقل سجلات، مزامنة إعدادات، أو منطق ما بعد الترقية. ليس **migration** (مخطط) ولا **seeder** (بيانات بذر قابلة للتكرار).

---

## متى تستخدم patch

| الأداة | الغرض |
|------|---------|
| **Migration** | CREATE/ALTER للجداول والأعمدة |
| **Seeder** | بيانات أولية أو نموذجية (تشغيل يدوي) |
| **Patch** | تشغيل مرة واحدة وتتبع في `history` |

أمثلة Patch:

- إصلاح صفوف غير صالحة بعد خطأ
- ملء قيم افتراضية للسجلات القديمة
- إعادة تسمية قيم إعدادات في DB
- منطق ما بعد التحديث بعد إصدار جديد

---

## مواقع الملفات

```text
vendor/pinoox/pincore/patches/     ← platform (CLI: platform)
apps/{package}/patches/            ← your app
```

> المسار القديم `database/patches/` **غير مستخدم**. Patches بجانب `app.php`، وليس تحت `database/`.

---

## إنشاء patch

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

يكتب CLI ملفًا مؤرّخًا، مثل:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

شكل القالب (فئة مجهولة):

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

مساحة أسماء platform: `Pinoox\Patches`.

---

## دوال PatchBase

| الدالة | الدور |
|--------|------|
| `up()` | المنطق الرئيسي (يُستدعى عبر `run()`) |
| `down()` | التراجع عندما `canRollback()` true |
| `shouldRun()` | إذا false، يُسجَّل patch كـ **skipped** |
| `canRollback()` | هل التراجع مسموح |
| `description()` | نص مقروء في history |
| `metadata()` | JSON إضافي في history |

---

## أوامر CLI

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**ملاحظة:** `patch:run` يشغّل **ترحيلات platform** أولًا، ثم patches للحزمة المختارة.

الاختصار: `php pinoox patch` = `patch:run`.

---

## جدول history

الترحيلات والـ patches يتشاركان جدول **`history`**:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Patches الناجحة لا تُعاد تشغيلها تلقائيًا.

---

## المُثبّت

تطبيق النظام `com_pinoox_installer` يشغّل الترحيلات والـ patches أثناء الإعداد عبر `SetupService`.

---

## أفضل الممارسات

- لا تعدّل patch نُفّذ بالفعل — أنشئ patch جديدًا.
- استخدم migrations للمخطط، وليس patches.
- نفّذ `shouldRun()` لفحوصات idempotent وتخطي العمل غير الضروري.
- فعّل rollback فقط عندما `down()` آمن.

---

## وثائق ذات صلة

- [الترحيلات (Migrations)](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [مرجع CLI](../start/cli-reference.md)

---

[← العودة إلى الفهرس](../README.md)
