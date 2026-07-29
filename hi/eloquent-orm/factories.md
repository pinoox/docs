# Test Data — Seeders

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x CLI में **Model Factory** (Laravel-style) शामिल नहीं है। Initial और development data के लिए recommended approach **Seeders** है `SeederBase` के साथ `apps/{package}/database/seeders/` में।

---

## Seeder बनाएँ

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## Structure

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

## दूसरा seeder call करें

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

## Model के साथ create

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

## Seeders चलाएँ

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

## Recommended order

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Production में seeders

- केवल **essential** data (roles, default settings)।
- Fake/dev data `APP_ENV` से guard करें:

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

## Seeder vs Patch

| Seeder | Patch |
|--------|-------|
| Initial / sample data | One-time fix for existing data |
| `seeder:run` — repeatable with caution | `patch:run` — tracked once |

---

## Tips

- Idempotent seeders लिखें (blind `insert` की जगह `firstOrCreate`)।
- Seeders में real credentials commit न करें।
- Unit tests के लिए Pest fixtures या `:memory:` sqlite उपयोग करें।

---

## संबंधित docs

- [Migrations](../database/migrations.md)
- [Eloquent getting started](./getting-started.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
