# Migrations

[← इंडेक्स पर वापस जाएँ](../README.md)

Migrations database में **schema** changes version करती हैं। Pinoox 3.x में app files `apps/{package}/database/migrations/` में और core files `system/database/migrations/` में रहती हैं।

---

## Migration बनाएँ

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

`$this->table('posts', $package)` सही app prefix apply करता है।

---

## Migrations चलाएँ

```bash
# app migration
php pinoox migrate com_acme_blog

# core migration
php pinoox migrate pincore

# platform migration (pinx_* tables)
php pinoox migrate platform
```

---

## Status और rollback

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

Core tables: prefix **`pincore_`** (या platform scope के लिए `pinx_`)।

---

## Namespaces

| Location | Namespace |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Core | `Pinoox\Database\migrations` |

---

## Legacy path

Pinoox पुराना `apps/{package}/migrations/` folder अभी भी read करता है, लेकिन **नई** files `database/migrations/` में create होती हैं।

---

## Migration vs Seed vs Patch

| Type | Purpose | Command |
|------|---------|---------|
| Migration | Schema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Initial data | `php pinoox seeder:run {package}` |
| Patch | One-time data change | `php pinoox patch:run {package}` |

पूरा patch guide: [Patches (data updates)](./patches.md).

---

## Best practices

- प्रति migration एक logical change (एक table या एक ALTER)।
- हमेशा `down()` लिखें।
- पहले से run migration edit न करें — नई बनाएँ।
- Core tables पर foreign keys `$this->table(Table::FILE, 'platform')` उपयोग करें।

---

## संबंधित docs

- [Database getting started](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
