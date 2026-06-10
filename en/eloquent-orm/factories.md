# Test Data — Seeders

Pinoox 3.x does not include a **Model Factory** (Laravel-style) in the CLI. The recommended approach for initial and development data is **Seeders** with `SeederBase` in `apps/{package}/database/seed/`.

---

## Create a seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seed/PostSeeder.php
```

---

## Structure

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

## Call another seeder

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

## create with Model

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

## Run seeders

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## Recommended order

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeders in production

- Only **essential** data (roles, default settings).
- Guard fake/dev data with `APP_ENV`:

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

- Write idempotent seeders (`firstOrCreate` instead of blind `insert`).
- Do not commit real credentials in seeders.
- For unit tests, use Pest fixtures or `:memory:` sqlite.

---

## Related docs

- [Migrations](../database/migrations.md)
- [Eloquent getting started](./getting-started.md)
- [App database structure](../../pinoox%20docs/pinoox-app-database-structure.md)
