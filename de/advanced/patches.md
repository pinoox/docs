# Patches (Datenaktualisierungen)

[← Zurück zum Index](../README.md)

Ein **Patch** in Pinoox 3.x ist eine **einmalige operative Änderung**: Daten korrigieren, Datensätze verschieben, Config synchronisieren oder Logik nach einem Upgrade ausführen. Es ist keine **Migration** (Schema) und kein **Seeder** (wiederholbare Seed-Daten).

---

## Wann einen Patch verwenden

| Werkzeug | Zweck |
|------|---------|
| **Migration** | Tabellen und Spalten CREATE/ALTER |
| **Seeder** | Anfangs- oder Beispieldaten (manuelle Ausführung) |
| **Patch** | Einmal ausführen und in `history` verfolgen |

Patch-Beispiele:

- Ungültige Zeilen nach einem Bug korrigieren
- Standardwerte für alte Datensätze nachfüllen
- Config-Werte in der DB umbenennen
- Logik nach einem neuen Release

---

## Dateispeicherorte

```text
vendor/pinoox/pincore/patches/     ← Plattform (CLI: platform)
apps/{package}/patches/            ← Ihre App
```

> Der Legacy-Pfad `database/patches/` wird **nicht verwendet**. Patches liegen neben `app.php`, nicht unter `database/`.

---

## Patch erstellen

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

Die CLI schreibt eine Datei mit Zeitstempel, z. B.:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

Stub-Form (anonyme Klasse):

```php
<?php
namespace App\com_acme_shop\patches;

use Pinoox\Component\Database\Patch\PatchBase;
use Pinoox\Portal\Database\DB;

return new class extends PatchBase
{
    public function description(): string
    {
        return 'Set empty contact status to active';
    }

    public function shouldRun(): bool
    {
        return DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->exists();
    }

    public function canRollback(): bool
    {
        return false;
    }

    public function up(): void
    {
        DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->update(['status' => 'active']);
    }
}
```

Plattform-Namespace: `Pinoox\Patches`.

---

## PatchBase-Methoden

| Methode | Rolle |
|--------|------|
| `up()` | Hauptlogik (über `run()` aufgerufen) |
| `down()` | Rückgängig machen, wenn `canRollback()` true ist |
| `shouldRun()` | Wenn false, wird der Patch als **skipped** protokolliert |
| `canRollback()` | Ob Rollback erlaubt ist |
| `description()` | Lesbarer Text in der History |
| `metadata()` | Zusätzliches JSON in der History |

---

## CLI-Befehle

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Hinweis:** `patch:run` führt zuerst **Plattform-Migrationen** aus, dann Patches für das gewählte Package.

Alias: `php pinoox patch` = `patch:run`.

---

## history-Tabelle

Migrationen und Patches teilen sich die **`history`**-Tabelle:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Erfolgreiche Patches werden nicht automatisch erneut ausgeführt.

---

## Installer

Die System-App `com_pinoox_installer` führt während des Setups Migrationen und Patches über `SetupService` aus.

---

## Best Practices

- Einen bereits ausgeführten Patch nicht bearbeiten — einen neuen erstellen.
- Schema über Migrationen, nicht über Patches.
- `shouldRun()` implementieren, damit idempotente Prüfungen unnötige Arbeit überspringen.
- Rollback nur aktivieren, wenn `down()` sicher ist.

---

## Verwandte Dokumentation

- [Migrationen](./migrations.md)
- [Seeder / Factories](../eloquent-orm/factories.md)
- [CLI-Referenz](../start/cli-reference.md)

---

[← Zurück zum Index](../README.md)
