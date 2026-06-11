# Migrations

[← Back to index](../README.md)

Migrations version **schema** changes in the database. In Pinoox 3.x, app files live in `apps/{package}/database/migrations/` and core files in `system/database/migrations/`.

---

## Create a migration

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Output:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## File structure

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

`$this->table('posts', $package)` applies the correct app prefix.

---

## Run migrations

```bash
# app migration
php pinoox migrate com_acme_blog

# core migration
php pinoox migrate pincore

# platform migration (pinx_* tables)
php pinoox migrate platform
```

---

## Status and rollback

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Core migration (example)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Core tables: prefix **`pincore_`** (or `pinx_` for platform scope).

---

## Namespaces

| Location | Namespace |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Core | `Pinoox\Database\migrations` |

---

## Legacy path

Pinoox still reads the old `apps/{package}/migrations/` folder, but **new** files are created in `database/migrations/`.

---

## Migration vs Seed vs Patch

| Type | Purpose | Command |
|------|---------|---------|
| Migration | Schema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Initial data | `php pinoox seeder:run {package}` |
| Patch | One-time data change | `php pinoox patch:run {package}` |

Full patch guide: [Patches (data updates)](./patches.md).

---

## Best practices

- One logical change per migration (one table or one ALTER).
- Always write `down()`.
- Do not edit a migration that has already run — create a new one.
- Foreign keys to core tables use `$this->table(Table::FILE, 'platform')`.

---

## Related docs

- [Database getting started](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← Back to index](../README.md)
