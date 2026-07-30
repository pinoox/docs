# Test verisi — Seeder'lar

[← Dizine dön](../README.md)

Pinoox 3.x CLI'da **Model Factory** (Laravel tarzı) içermez. Başlangıç ve geliştirme verisi için önerilen yaklaşım `apps/{package}/database/seeders/` içinde **`SeederBase`** ile **Seeder**'lardır.

---

## Seeder oluşturma

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## Yapı

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

## Başka seeder çağırma

```php
public function run(): void
{
    $this->call([
        'RoleSeeder',
        'UserSeeder',
    ]);

    // dependent data after users
    PostModel::factory(); // ❌ not available — use insert or create manually
}
```

---

## Model ile create

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

## Seeder'ları çalıştırma

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
php pinoox seeder:run platform
```

`-c` matches the **file basename** (e.g. `PostSeeder`). App install does **not** auto-run seeders.

---

## Call Seeders From Code

Use `Pinoox\Portal\Database\Seeder`, or `$this->seed()` / `$this->seedAll()` in migrations and patches.

```php
use Pinoox\Portal\Database\Seeder;

Seeder::run('PostSeeder');
Seeder::run('PostSeeder', 'com_acme_blog');
Seeder::run(['RoleSeeder', 'PostSeeder']);
Seeder::run(DatabaseSeeder::class);
Seeder::runAll();
Seeder::runAll('platform');
```

```php
// migration / patch
$this->seed('GatewaySeeder');
$this->seedAll();
```

```php
// from another seeder
$this->call(['RoleSeeder', 'UserSeeder']);
```

See English docs: [Factories and seeders](../../en/eloquent-orm/factories.md#call-seeders-from-code).

---

## Önerilen sıra

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Üretimde seeder'lar

- Yalnızca **gerekli** veri (roller, varsayılan ayarlar).
- Sahte/geliştirme verisini `APP_ENV` ile koruyun:

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

## Seeder ve Patch

| Seeder | Patch |
|--------|-------|
| Başlangıç / örnek veri | Mevcut veri için tek seferlik düzeltme |
| `seeder:run` — dikkatli tekrarlanabilir | `patch:run` — bir kez izlenir |

---

## İpuçları

- İdempotent seeder'lar yazın (kör `insert` yerine `firstOrCreate`).
- Seeder'lara gerçek kimlik bilgilerini commit etmeyin.
- Birim testleri için Pest fixture'ları veya `:memory:` sqlite kullanın.

---

## İlgili dokümantasyon

- [Migration'lar](../database/migrations.md)
- [Eloquent'e başlarken](./getting-started.md)
- [Uygulama veritabanı yapılandırması (app.php)](../start/app-manifest.md)

---

[← Dizine dön](../README.md)
