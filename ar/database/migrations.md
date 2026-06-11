# الترحيلات (Migrations)

[← العودة إلى الفهرس](../README.md)

الترحيلات تُ versioning تغييرات **المخطط (schema)** في قاعدة البيانات. في Pinoox 3.x، ملفات التطبيق في `apps/{package}/database/migrations/` وملفات النواة في `system/database/migrations/`.

---

## إنشاء ترحيل

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

المخرجات:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## بنية الملف

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

`$this->table('posts', $package)` يطبّق بادئة التطبيق الصحيحة.

---

## تشغيل الترحيلات

```bash
# app migration
php pinoox migrate com_acme_blog

# core migration
php pinoox migrate pincore

# platform migration (pinx_* tables)
php pinoox migrate platform
```

---

## الحالة والتراجع

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## ترحيل النواة (مثال)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

جداول النواة: البادئة **`pincore_`** (أو `pinx_` لنطاق platform).

---

## مساحات الأسماء

| الموقع | مساحة الأسماء |
|----------|-----------|
| التطبيق | `App\{package}\database\migrations` |
| النواة | `Pinoox\Database\migrations` |

---

## المسار القديم

Pinoox ما زال يقرأ مجلد `apps/{package}/migrations/` القديم، لكن **الملفات الجديدة** تُنشأ في `database/migrations/`.

---

## Migration مقابل Seed مقابل Patch

| النوع | الغرض | الأمر |
|------|---------|---------|
| Migration | المخطط (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | بيانات أولية | `php pinoox seeder:run {package}` |
| Patch | تغيير بيانات لمرة واحدة | `php pinoox patch:run {package}` |

دليل Patch الكامل: [Patches (تحديثات البيانات)](./patches.md).

---

## أفضل الممارسات

- تغيير منطقي واحد لكل ترحيل (جدول واحد أو ALTER واحد).
- اكتب `down()` دائمًا.
- لا تعدّل ترحيلًا نُفّذ بالفعل — أنشئ ترحيلًا جديدًا.
- المفاتيح الأجنبية لجداول النواة تستخدم `$this->table(Table::FILE, 'platform')`.

---

## وثائق ذات صلة

- [البدء مع قاعدة البيانات](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [إعداد قاعدة بيانات التطبيق (app.php)](../start/app-manifest.md)

---

[← العودة إلى الفهرس](../README.md)
