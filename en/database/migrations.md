# Migrations

[← Back to index](../README.md)

Migrations version **schema** changes in the database. In Pinoox 3.x, app files live in `apps/{package}/database/migrations/` and core files in `pincore/database/migrations/` (`~pincore/database/migrations`).

---

## Create a migration

```bash
php pinoox migrate:create posts com_acme_blog
php pinoox migrate:create CreatePosts com_acme_blog
php pinoox migrate:create create_posts_table com_acme_blog
```

All three write:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

Aliases: `mg:create`, `mg:make`, `make:migration`.

### Naming (Laravel-style)

The stub is chosen from the name, or from `--create` / `--table`:

| Input | File | Stub |
|-------|------|------|
| `posts` / `CreatePosts` / `create_posts_table` | `create_posts_table.php` | `$this->schema->create()` |
| `add_email_to_users` | `add_email_to_users.php` | `$this->schema->table()` |
| `drop_posts_table` | `drop_posts_table.php` | `$this->schema->dropIfExists()` |
| `sync_legacy_flags --table=users` | `sync_legacy_flags.php` | `$this->schema->table()` |
| `add_status --create=orders` | `add_status.php` | `$this->schema->create()` |

```bash
php pinoox migrate:create add_email_to_users com_acme_blog
php pinoox migrate:create drop_posts_table com_acme_blog
php pinoox migrate:create sync_legacy_flags com_acme_blog --table=users
php pinoox make:migration add_status --create=orders com_acme_blog
```

`migrate:drop` **hard-drops tables** and clears history. To scaffold a DROP/ALTER file, use `migrate:create drop_*_table` (or `add_*` / `--table=`).

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

# platform / core migration (pinx_* tables)
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

Core tables: prefix **`pinx_`**.

---

## Namespaces

| Location | Namespace |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Core | `Pinoox\Database\migrations` |

---

## Legacy path

New and existing files are loaded only from `database/migrations/`. The old `apps/{package}/migrations/` folder is **not** scanned.

---

## Migration vs Seed vs Patch

| Type | Purpose | Command |
|------|---------|---------|
| Migration | Schema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Initial data (manual / explicit call) | `php pinoox seeder:run {package}` or `$this->seed()` |
| Patch | One-time data change | `php pinoox patch:run {package}` |

App install runs migrations and patches — **not** seeders. To seed on install, call `$this->seed('Name')` or `$this->seedAll()` from a migration (or patch). See [Factories and seeders](../eloquent-orm/factories.md#call-seeders-from-code).

Full patch guide: [Patches (data updates)](../advanced/patches.md).

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
