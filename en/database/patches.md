# Patches (data updates)

[← Back to index](../../README.md)

A **patch** in Pinoox 3.x is a **one-time operational change**: fix data, move records, sync config, or run post-upgrade logic. It is not a **migration** (schema) or a **seeder** (repeatable seed data).

---

## When to use a patch

| Tool | Purpose |
|------|---------|
| **Migration** | CREATE/ALTER tables and columns |
| **Seeder** | Initial or sample data (manual runs) |
| **Patch** | Run once and track in `history` |

Patch examples:

- Fix invalid rows after a bug
- Backfill defaults for old records
- Rename config values in DB
- Post-update logic after a new release

---

## File locations

```text
vendor/pinoox/pincore/patches/     ← platform (CLI: platform)
apps/{package}/patches/            ← your app
```

> Legacy path `database/patches/` is **not used**. Patches live next to `app.php`, not under `database/`.

---

## Create a patch

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

The CLI writes a timestamped file, e.g.:

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

**Note:** `patch:run` runs **platform migrations** first, then patches for the selected package.

Alias: `php pinoox patch` = `patch:run`.

---

## history table

Migrations and patches share the **`history`** table:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Successful patches are not re-run automatically.

---

## Installer

The system app `com_pinoox_installer` runs migrations and patches during setup via `SetupService`.

---

## Best practices

- Do not edit a patch that already ran — create a new one.
- Use migrations for schema, not patches.
- Implement `shouldRun()` so idempotent checks skip unnecessary work.
- Enable rollback only when `down()` is safe.

---

## Related docs

- [Migrations](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [CLI reference](../start/cli-reference.md)

---

[← Back to index](../../README.md)
