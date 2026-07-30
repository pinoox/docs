# داده آزمایشی — Seeder

[← بازگشت به فهرست](../README.md)

پینوکس 3.x **Model Factory** (Laravel-style) در CLI ندارد. روش پیشنهادی برای داده اولیه و توسعه: **Seeder** با `SeederBase` در `apps/{package}/database/seeders/`.

---

## ساخت Seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## ساختار

```php
<?php
namespace App\com_acme_blog\database\seeders;

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

با نام فایل (basename) یا کلاس نام‌دار که از `SeederBase` ارث ببرد:

```php
public function run(): void
{
    $this->call([
        'RoleSeeder',
        'UserSeeder',
        // NamedClassSeeder::class,
    ]);
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

## اجرا از CLI

`-c` / `--class` با **نام فایل** seeder مطابقت دارد (مثلاً `PostSeeder`)، نه نام کلاس anonymous.

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
php pinoox seeder:run platform
```

با Pinx: `pinx seed` / `pinx seed -c PostSeeder`. دستور `pinx setup` ممکن است seederها را اجرا کند (`--skip-seed` برای رد کردن). **نصب/به‌روزرسانی اپ به‌صورت خودکار seeder اجرا نمی‌کند.**

---

## فراخوانی از کد (Migration / Patch / Portal)

از `Pinoox\Portal\Database\Seeder` یا متدهای `$this->seed()` / `$this->seedAll()` داخل migration و patch استفاده کنید. Seed فقط وقتی لازم است صریحاً صدا زده می‌شود.

```php
use Pinoox\Portal\Database\Seeder;

Seeder::run('PostSeeder');                        // پکیج فعلی (PackageContext)
Seeder::run('PostSeeder', 'com_acme_blog');        // اپ دیگر
Seeder::run(['RoleSeeder', 'PostSeeder']);         // چند seeder با نام فایل
Seeder::run(DatabaseSeeder::class);               // کلاس نام‌دار که از SeederBase ارث می‌برد
Seeder::runAll();                                 // همهٔ seederهای پکیج فعلی
Seeder::runAll('platform');                       // همهٔ seederهای پلتفرم
Seeder::runAll('com_acme_blog');
```

### Seeder با کلاس نام‌دار

فایل‌های anonymous (stub فعلی CLI) همچنان کار می‌کنند. می‌توانید کلاس نام‌دار هم تعریف کنید و با `::class` صدا بزنید:

```php
namespace App\com_acme_blog\database\seeders;

use Pinoox\Component\Database\Seeder\SeederBase;

class DatabaseSeeder extends SeederBase
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PostSeeder::class,
        ]);
    }
}

// migration / patch / هرجا:
Seeder::run(DatabaseSeeder::class);
$this->seed(DatabaseSeeder::class);
```

کلاس باید autoload شود (PSR-4) یا قبل از `Seeder::run()` require شده باشد. برای فراخوانی با `::class` الزامی نیست که داخل `database/seeders/` باشد؛ `runAll()` و CLI فقط همان پوشه را بار می‌کنند.

### داخل migration

```php
public function up(): void
{
    // schema...
    $this->seed('GatewaySeeder');
    // یا همهٔ seederهای همین پکیج:
    // $this->seedAll();
}
```

### داخل patch

```php
public function up(): void
{
    $this->seed('GatewaySeeder');
    // $this->seedAll('com_other_app');
}
```

منطق سنگین را در Service بگذارید (idempotent، مثلاً `importIfEmpty`) و هم از seeder و هم از migration همان را صدا بزنید.

---

## ترتیب پیشنهادی

1. `php pinoox migrate com_acme_blog`
2. در صورت نیاز: صدا زدن از migration/patch، یا `php pinoox seeder:run com_acme_blog`

---

## Seeder در محیط production

- فقط داده **ضروری** (نقش‌ها، تنظیمات پیش‌فرض).
- داده fake/dev را با `APP_ENV` guard کنید.
- به نصب اپ برای seed اتکا نکنید — دادهٔ لازم برای کارکرد را صریحاً در migration یا patch صدا بزنید.

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
| موقع نصب اپ اجرا نمی‌شود | موقع نصب/آپدیت اجرا می‌شود (مگر skip) |

---

## نکات

- Seeder را idempotent بنویسید (`firstOrCreate` به‌جای `insert` کور).
- credential واقعی در seeder commit نکنید.
- برای تست واحد از Pest fixture یا `:memory:` sqlite استفاده کنید.

---

## مستندات مرتبط

- [Migration — مهاجرت](../database/migrations.md)
- [شروع به کار Eloquent](./getting-started.md)
- [پیکربندی DB اپ (app.php)](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)
