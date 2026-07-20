# Patches (data updates)

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x में **patch** एक **one-time operational change** है: data fix, records move, config sync, या post-upgrade logic चलाना। यह **migration** (schema) या **seeder** (repeatable seed data) नहीं है।

---

## Patch कब उपयोग करें

| Tool | Purpose |
|------|---------|
| **Migration** | CREATE/ALTER tables and columns |
| **Seeder** | Initial or sample data (manual runs) |
| **Patch** | Run once and track in `history` |

Patch examples:

- Bug के बाद invalid rows fix करना
- पुरane records के लिए defaults backfill करना
- DB में config values rename करना
- नए release के बाद post-update logic

---

## File locations

```text
vendor/pinoox/pincore/patches/     ← platform (CLI: platform)
apps/{package}/patches/            ← your app
```

> Legacy path `database/patches/` **उपयोग नहीं होता**। Patches `app.php` के पास रहते हैं, `database/` के अंतर्गत नहीं।

---

## Patch बनाएँ

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

CLI timestamped file लिखता है, जैसे:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

Stub shape (anonymous class):

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

Platform namespace: `Pinoox\Patches`.

---

## PatchBase methods

| Method | Role |
|--------|------|
| `up()` | Main logic (called via `run()`) |
| `down()` | Revert when `canRollback()` is true |
| `shouldRun()` | If false, patch is recorded as **skipped** |
| `canRollback()` | Whether rollback is allowed |
| `description()` | Human-readable text in history |
| `metadata()` | Extra JSON stored in history |

---

## CLI commands

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Note:** `patch:run` पहले **platform migrations** चलाता है, फिर selected package के patches।

Alias: `php pinoox patch` = `patch:run`.

---

## history table

Migrations और patches **`history`** table share करते हैं:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Successful patches automatically दोबारा run नहीं होते।

---

## Installer

System app `com_pinoox_installer` setup के दौरान `SetupService` के ज़रिए migrations और patches चलाता है।

---

## Best practices

- पहले से run patch edit न करें — नया बनाएँ।
- Schema के लिए migrations उपयोग करें, patches नहीं।
- Idempotent checks के लिए `shouldRun()` implement करें ताकि unnecessary work skip हो।
- Rollback तभी enable करें जब `down()` safe हो।

---

## संबंधित docs

- [Migrations](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [CLI reference](../start/cli-reference.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
