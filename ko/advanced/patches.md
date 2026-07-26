# Patches (data updates)

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 **patch**는 **일회성 운영 변경**입니다: data 수정, record 이동, config 동기화, upgrade 후 logic 실행. **migration**(schema)이나 **seeder**(반복 가능 seed data)가 아닙니다.

---

## Patch 사용 시점

| Tool | Purpose |
|------|---------|
| **Migration** | CREATE/ALTER table과 column |
| **Seeder** | 초기 또는 sample data (수동 실행) |
| **Patch** | 한 번 실행하고 `history`에 기록 |

Patch 예:

- bug 후 invalid row 수정
- 오래된 record에 default backfill
- DB에서 config 값 rename
- 새 릴리스 후 post-update logic

---

## File 위치

```text
vendor/pinoox/pincore/patches/     ← platform (CLI: platform)
apps/{package}/patches/            ← your app
```

> Legacy path `database/patches/`는 **사용하지 않습니다**. Patch는 `app.php` 옆에 있으며 `database/` 아래가 아닙니다.

---

## Patch 생성

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

CLI가 timestamp file을 작성합니다, 예:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

Stub 형태 (anonymous class):

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

## PatchBase method

| Method | Role |
|--------|------|
| `up()` | Main logic (called via `run()`) |
| `down()` | Revert when `canRollback()` is true |
| `shouldRun()` | false이면 patch가 **skipped**로 기록 |
| `canRollback()` | rollback 허용 여부 |
| `description()` | history의 human-readable text |
| `metadata()` | history에 저장되는 extra JSON |

---

## CLI command

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Note:** `patch:run`은 선택 package의 patch 전에 **platform migrations**를 먼저 실행합니다.

Alias: `php pinoox patch` = `patch:run`.

---

## history table

Migration과 patch는 **`history`** table을 공유합니다:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

성공한 patch는 자동으로 재실행되지 않습니다.

---

## Installer

시스템 앱 `com_pinoox_installer`는 `SetupService`를 통해 setup 중 migration과 patch를 실행합니다.

---

## Best practices

- 이미 실행된 patch는 편집하지 말고 새로 생성.
- schema에는 migration 사용, patch 아님.
- idempotent check로 불필요한 작업을 skip하려면 `shouldRun()` 구현.
- `down()`이 안전할 때만 rollback 활성화.

---

## 관련 문서

- [Migrations](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [CLI reference](../start/cli-reference.md)

---

[← 색인으로 돌아가기](../README.md)
