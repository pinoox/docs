# Test Data - Factories and Seeders

[Back to index](../README.md)

Pinoox supports Laravel-style model factories in `apps/{package}/database/factories/`.
Use factories for repeatable test/dev records and seeders for initial or demo data.

---

## Create a Factory

```bash
php pinoox factory:create PostFactory com_acme_blog
```

```text
apps/com_acme_blog/database/factories/PostFactory.php
```

```php
<?php
namespace App\com_acme_blog\database\factories;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Factories\Factory;

class PostFactory extends Factory
{
    protected ?string $model = PostModel::class;

    public function definition(): array
    {
        return [
            'user_id' => 1,
            'title' => 'Sample post',
            'body' => 'Sample content',
            'status' => 'draft',
        ];
    }
}
```

Use it from tests or seeders:

```php
PostModel::factory()->make();
PostModel::factory()->create();
PostModel::factory()->count(10)->create();
PostModel::factory()->state(['status' => 'published'])->create();
```

Factories also support `sequence()`, `raw()`, `afterMaking()`, and `afterCreating()`.
If `fakerphp/faker` is installed, call `$this->faker()` inside `definition()`.

---

## Create a Seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

```php
<?php
namespace App\com_acme_blog\database\seeders;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Seeder\SeederBase;

return new class extends SeederBase
{
    public function run(): void
    {
        PostModel::factory()
            ->count(20)
            ->sequence(
                ['status' => 'published'],
                ['status' => 'draft'],
            )
            ->create();
    }
};
```

---

## Create With Model

Factories are optional. You can still write seeders manually:

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

## Run Seeders

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## Recommended Order

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeders In Production

- Only seed essential data such as roles and default settings.
- Guard fake/dev data with `APP_ENV`.

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }

    PostModel::factory()->count(20)->create();
}
```

---

## Seeder Vs Patch

| Seeder | Patch |
|--------|-------|
| Initial or sample data | One-time fix for existing data |
| `seeder:run` is repeatable with caution | `patch:run` is tracked once |

---

## Tips

- Keep factories focused on one model's default attributes.
- Use factory states for variants such as `published` or `admin`.
- Write seeders idempotently when they may run more than once.
- Do not commit real credentials in seeders or factories.
- For unit tests, prefer factories with `make()` or `:memory:` sqlite.

---

## Related Docs

- [Migrations](../database/migrations.md)
- [Eloquent getting started](./getting-started.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[Back to index](../README.md)
