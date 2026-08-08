# Migrations

[← Back to index](../README.md)

Migrations version **schema** changes: create tables, add columns, drop indexes, and keep every app’s database in sync. Each file has `up()` (apply) and `down()` (undo).

| | App | Platform / core |
|--|-----|-----------------|
| Path | `apps/{package}/database/migrations/` | `pincore/database/migrations/` (`~pincore/…`) |
| Namespace | `App\{package}\database\migrations` | `Pinoox\Database\migrations` |
| Prefix | from `app.php` / `DB_PREFIX` | `pinx_` |
| Run | `php pinoox migrate {package}` | `php pinoox migrate platform` |

Only `database/migrations/` is scanned. The old `apps/{package}/migrations/` folder is ignored.

**Single-app (Pinx):** files live at the project root (`database/migrations/`). Use `pinx migrate…` — no package argument.

---

## Quick start

```bash
# multi-app platform
php pinoox migrate:create posts com_acme_blog
php pinoox migrate com_acme_blog

# Pinx single-app
pinx migrate:create posts
pinx migrate
```

Open the generated file, add columns, then run migrate. History is stored in the platform `history` table (`pinx_history`) per package.

Aliases for create: `mg:create`, `mg:make`, `make:migration`.

---

## Anatomy of a file

Generated files are anonymous classes that extend `MigrationBase`:

```php
<?php

namespace App\com_acme_blog\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists('posts');
    }
};
```

`$this->schema` is already bound to the **current package** connection. The logical name `'posts'` is enough — prefix is applied automatically.

These two are equivalent:

```php
$this->schema->create('posts', function (Blueprint $table) { /* ... */ });
$this->schema->create($this->table('posts'), function (Blueprint $table) { /* ... */ });
```

Pass a package only when this file must touch **another** app:

```php
$this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
    // ...
});
```

Always implement `down()` so rollback can undo the change.

---

## Create a migration (naming)

The **file name** chooses the stub. You can also force it with `--create=` / `--table=`.

| You type | File basename | Stub |
|----------|---------------|------|
| `posts` / `CreatePosts` / `create_posts_table` | `create_posts_table` | create table |
| `add_email_to_users` | `add_email_to_users` | alter table |
| `drop_posts_table` | `drop_posts_table` | drop table |
| `sync_legacy_flags --table=users` | `sync_legacy_flags` | alter `users` |
| `add_status --create=orders` | `add_status` | create `orders` |
| `fix_legacy_flags` (no known verb) | `fix_legacy_flags` | empty `up`/`down` |

```bash
php pinoox migrate:create posts com_acme_blog
php pinoox migrate:create add_email_to_users com_acme_blog
php pinoox migrate:create drop_posts_table com_acme_blog
php pinoox migrate:create sync_legacy_flags com_acme_blog --table=users
php pinoox make:migration add_status --create=orders com_acme_blog

pinx migrate:create add_email_to_users
pinx make migration sync_legacy_flags --table=users
```

Output path example:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

`migrate:drop` **destroys tables** and clears history. To *scaffold* a DROP/ALTER file, use `migrate:create drop_*_table` (or `add_*` / `--table=`).

---

## Examples

### 1) New table

```bash
php pinoox migrate:create create_comments_table com_acme_blog
```

```php
public function up()
{
    $this->schema->create('comments', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('post_id');
        $table->string('author', 120);
        $table->text('body');
        $table->boolean('approved')->default(false);
        $table->timestamps();

        $table->index('post_id');
    });
}

public function down(): void
{
    $this->schema->dropIfExists('comments');
}
```

Useful column helpers: `id()`, `string()`, `text()`, `integer()`, `unsignedInteger()`, `boolean()`, `json()`, `timestamp()`, `timestamps()`, `softDeletes()`, `index()`, `unique()`.

### 2) Add / change a column

```bash
php pinoox migrate:create add_email_to_users com_acme_blog
```

```php
public function up()
{
    $this->schema->table('users', function (Blueprint $table) {
        $table->string('email', 190)->nullable()->after('name');
        $table->unique('email');
    });
}

public function down(): void
{
    $this->schema->table('users', function (Blueprint $table) {
        $table->dropUnique(['email']);
        $table->dropColumn('email');
    });
}
```

`after()` is supported on MySQL/MariaDB. Always reverse the change in `down()`.

### 3) Drop a table

```bash
php pinoox migrate:create drop_legacy_logs_table com_acme_blog
```

```php
public function up()
{
    $this->schema->dropIfExists('legacy_logs');
}

public function down(): void
{
    $this->schema->create('legacy_logs', function (Blueprint $table) {
        $table->id();
        $table->text('message')->nullable();
        $table->timestamps();
    });
}
```

### 4) Foreign key to core (`user`, `file`, …)

From an **app** migration, point at platform tables with `[name, 'platform']`:

```php
use Pinoox\Model\Table;

public function up()
{
    $this->schema->create('posts', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('user_id')->nullable();
        $table->unsignedInteger('cover_id')->nullable();
        $table->string('title');
        $table->timestamps();

        $table->foreign('user_id')
            ->references('user_id')
            ->on([Table::USER, 'platform'])
            ->nullOnDelete();

        $table->foreign('cover_id')
            ->references('file_id')
            ->on([Table::FILE, 'platform'])
            ->nullOnDelete();
    });
}
```

`$this->foreignTable('user', 'platform')` returns a raw physical name when you need it outside `->on()`.

### 5) Seed once when the table is created

Install runs **migrations + patches**, not seeders. Call a seeder from `up()` if the app must ship with data:

```php
public function up()
{
    $this->schema->create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });

    $this->seed('CategorySeeder');   // one file
    // $this->seedAll();             // every seeder in the package
}
```

See [Factories and seeders](../eloquent-orm/factories.md#call-seeders-from-code).

---

## Run, status, rollback

```bash
php pinoox migrate com_acme_blog
php pinoox migrate platform          # core pinx_* tables (also runs first when you migrate an app)
php pinoox migrate --devdb           # local DevDB
php pinoox migrate --ignore-fk       # MySQL/MariaDB: disable FK checks for this run
php pinoox migrate --force           # continue even if some tables already exist

php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog          # last batch
php pinoox migrate:rollback com_acme_blog --step=2
php pinoox migrate:rollback com_acme_blog --all
```

Each successful **file** is one batch, so `--step=1` undoes the last file.

| Command | What it does |
|---------|----------------|
| `migrate` | Run pending `up()` |
| `migrate:status` | Done vs pending |
| `migrate:rollback` | Call `down()` (last N batches) |
| `migrate:reset` | `down()` all batches |
| `migrate --refresh` / `migrate:reset` then `migrate` | Undo all via `down()`, then migrate again |
| `migrate:fresh` / `migrate --fresh` | **Drop tables**, clear history, migrate |
| `migrate:drop` | **Drop tables** and clear history (no re-run) |

`fresh` / `drop` skip `down()` and hard-drop tables inferred from filenames. The platform history table itself is never dropped. Confirm unless you pass `--force`.

Pinx (current app only):

```bash
pinx migrate
pinx migrate --platform              # platform then app
pinx migrate:st
pinx migrate:rb --step=1
pinx migrate --fresh
pinx migrate:drop --force
```

---

## Platform / core

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;
use Illuminate\Database\Schema\Blueprint;

$this->schema->create(Table::USER, function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Core logical names: `Table::USER`, `FILE`, `TOKEN`, `HISTORY`, `ROLE`, `PERMISSION`, … Physical prefix: **`pinx_`**.

Migrating an app always runs **platform** first so core tables exist before app FKs.

---

## `$this->schema` helpers

| Method | Use |
|--------|-----|
| `create($name, fn)` | New table |
| `table($name, fn)` | Alter existing table |
| `drop` / `dropIfExists` | Remove table |
| `hasTable` / `hasColumn` | Guards inside `up()` |
| `disableForeignKeyConstraints()` | Bulk DDL (re-enable after) |

Inside the Blueprint callback: types (`string`, `text`, `integer`, `json`, …), defaults, `nullable()`, `unique()`, `index()`, `foreign()`, `timestamps()`, `softDeletes()`.

---

## Migration vs Seed vs Patch

| Type | Purpose | Command |
|------|---------|---------|
| Migration | Schema (CREATE/ALTER) | `php pinoox migrate {package}` / `pinx migrate` |
| Seeder | Initial / demo rows | `php pinoox seeder:run` or `$this->seed()` |
| Patch | One-time **data** fix (not schema) | `php pinoox patch:run {package}` |

App install runs migrations and patches — **not** seeders. Full patch guide: [Patches](../advanced/patches.md).

---

## Tips

- One logical change per file (one table or one ALTER).
- Never edit a migration that already ran on any environment — add a new file.
- Keep `down()` accurate; otherwise `rollback` / `reset` / `--refresh` will fail.
- Prefer `dropIfExists` / `nullable` FKs over assuming empty databases.
- Local DevDB: [DevDB](../start/devdb.md) and `php pinoox migrate --devdb` / `pinx migrate`.

---

## Related docs

- [Database getting started](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [App database configuration (`app.php`)](../start/app-manifest.md)
- [Pinx CLI](../start/pinx-cli.md)
- [Pinoox CLI reference](../start/cli-reference.md)

---

[← Back to index](../README.md)
