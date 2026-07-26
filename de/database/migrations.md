# Migrationen

[← Zurück zur Übersicht](../README.md)

Migrationen versionieren **Schema**-Änderungen in der Datenbank. In Pinoox 3.x liegen App-Dateien in `apps/{package}/database/migrations/` und Core-Dateien in `system/database/migrations/`.

---

## Eine Migration erstellen

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Ausgabe:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## Dateistruktur

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

`$this->table('posts', $package)` wendet das richtige App-Präfix an.

---

## Migrationen ausführen

```bash
# App-Migration
php pinoox migrate com_acme_blog

# Core-Migration
php pinoox migrate pincore

# Plattform-Migration (pinx_*-Tabellen)
php pinoox migrate platform
```

---

## Status und Rollback

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Core-Migration (Beispiel)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Core-Tabellen: Präfix **`pincore_`** (oder `pinx_` für den Plattform-Scope).

---

## Namespaces

| Ort | Namespace |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Core | `Pinoox\Database\migrations` |

---

## Legacy-Pfad

Pinoox liest weiterhin den alten Ordner `apps/{package}/migrations/`, **neue** Dateien werden jedoch in `database/migrations/` erstellt.

---

## Migration vs. Seed vs. Patch

| Typ | Zweck | Befehl |
|------|---------|---------|
| Migration | Schema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Initiale Daten | `php pinoox seeder:run {package}` |
| Patch | Einmalige Datenänderung | `php pinoox patch:run {package}` |

Vollständige Patch-Anleitung: [Patches (Datenaktualisierungen)](../advanced/patches.md).

---

## Best Practices

- Eine logische Änderung pro Migration (eine Tabelle oder ein ALTER).
- Schreiben Sie immer `down()`.
- Bearbeiten Sie keine bereits ausgeführte Migration — erstellen Sie eine neue.
- Fremdschlüssel auf Core-Tabellen verwenden `$this->table(Table::FILE, 'platform')`.

---

## Verwandte Dokumente

- [Erste Schritte mit der Datenbank](./getting-started.md)
- [Seeder / Factories](../eloquent-orm/factories.md)
- [App-Datenbankkonfiguration (app.php)](../start/app-manifest.md)

---

[← Zurück zur Übersicht](../README.md)
