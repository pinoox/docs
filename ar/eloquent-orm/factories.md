# بيانات الاختبار — Seeders

[← العودة إلى الفهرس](../README.md)

لا يتضمن Pinoox 3.x **Model Factory** (بأسلوب Laravel) في CLI. الأسلوب الموصى به للبيانات الأولية والتطوير هو **Seeders** مع `SeederBase` في `apps/{package}/database/seed/`.

---

## إنشاء seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seed/PostSeeder.php
```

---

## البنية

```php
<?php
namespace App\com_acme_blog\database\seed;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Seeder\SeederBase;
use Pinoox\Portal\Hash;

return new class extends SeederBase
{
    public function run(): void
    {
        PostModel::insert([
            [
                'user_id' => 1,
                'title' => 'First post',
                'body' => 'Sample content',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Second post',
                'body' => '...',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
```

---

## استدعاء seeder آخر

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
    ]);

    // dependent data after users
    PostModel::factory(); // ❌ not available — use insert or create manually
}
```

---

## create مع Model

```php
for ($i = 1; $i <= 20; $i++) {
    PostModel::create([
        'user_id' => 1,
        'title' => "Post #{$i}",
        'body' => 'Lorem ipsum',
        'status' => $i % 2 ? 'published' : 'draft',
    ]);
}
```

---

## تشغيل seeders

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## الترتيب الموصى به

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeders في الإنتاج

- **بيانات أساسية** فقط (أدوار، إعدادات افتراضية).
- احمِ بيانات وهمية/تطوير بـ `APP_ENV`:

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // sample data
}
```

---

## Seeder مقابل Patch

| Seeder | Patch |
|--------|-------|
| بيانات أولية / نموذجية | إصلاح لمرة واحدة للبيانات الموجودة |
| `seeder:run` — قابل للتكرار بحذر | `patch:run` — يُتتبع مرة واحدة |

---

## نصائح

- اكتب seeders idempotent (`firstOrCreate` بدل `insert` أعمى).
- لا تُلحق بيانات اعتماد حقيقية في seeders.
- لاختبارات الوحدة، استخدم fixtures Pest أو sqlite `:memory:`.

---

## وثائق ذات صلة

- [الترحيلات (Migrations)](../database/migrations.md)
- [البدء مع Eloquent](./getting-started.md)
- [إعداد قاعدة بيانات التطبيق (app.php)](../start/app-manifest.md)

---

[← العودة إلى الفهرس](../README.md)
