# داده آزمایشی — Seeder

[← بازگشت به فهرست](../../readme-fa.md)

پینوکس 3.x **Model Factory** (Laravel-style) در CLI ندارد. روش پیشنهادی برای داده اولیه و توسعه: **Seeder** با `SeederBase` در `apps/{package}/database/seed/`.

---

## ساخت Seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seed/PostSeeder.php
```

---

## ساختار

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
                'title' => 'اولین پست',
                'body' => 'متن نمونه',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'پست دوم',
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

## فراخوانی Seeder دیگر

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
    ]);

    // داده وابسته بعد از user
    PostModel::factory(); // ❌ وجود ندارد — insert یا create دستی
}
```

---

## create با Model

```php
for ($i = 1; $i <= 20; $i++) {
    PostModel::create([
        'user_id' => 1,
        'title' => "پست شماره {$i}",
        'body' => 'Lorem ipsum',
        'status' => $i % 2 ? 'published' : 'draft',
    ]);
}
```

---

## اجرا

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## ترتیب پیشنهادی

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeder در محیط production

- فقط داده **ضروری** (نقش‌ها، تنظیمات پیش‌فرض).
- داده fake/dev را با `APP_ENV` guard کنید:

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

## تفاوت Seeder و Patch

| Seeder | Patch |
|--------|-------|
| داده اولیه / نمونه | اصلاح یک‌باره داده موجود |
| `seeder:run` — قابل تکرار با احتیاط | `patch:run` — یک بار track می‌شود |

---

## نکات

- Seeder را idempotent بنویسید (`firstOrCreate` به‌جای `insert` کور).
- credential واقعی در seeder commit نکنید.
- برای تست واحد از Pest fixture یا `:memory:` sqlite استفاده کنید.

---

## مستندات مرتبط

- [Migration — مهاجرت](../database/migrations.md)
- [شروع به کار Eloquent](./getting-started.md)
- [ساختار DB اپ](../../pinoox%20docs/pinoox-app-database-structure.md)

---

[← بازگشت به فهرست](../../readme-fa.md)
