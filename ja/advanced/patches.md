# Patches（データ更新）

[← 索引に戻る](../README.md)

Pinoox 3.x の **Patch** は **一度限りの運用変更** です: データ修正、レコード移動、設定同期、またはアップグレード後ロジックの実行。**Migration**（スキーマ）や **Seeder**（繰り返し可能な Seed データ）ではありません。

---

## Patch を使うタイミング

| ツール | 目的 |
|------|---------|
| **Migration** | テーブルとカラムの CREATE/ALTER |
| **Seeder** | 初期またはサンプルデータ（手動実行） |
| **Patch** | 1 回実行して `history` に記録 |

Patch の例:

- バグ後の無効行の修正
- 古いレコードへのデフォルト値のバックフィル
- DB 内の設定値のリネーム
- 新リリース後の更新ロジック

---

## ファイルの場所

```text
vendor/pinoox/pincore/patches/     ← platform（CLI: platform）
apps/{package}/patches/            ← あなたのアプリ
```

> レガシーパス `database/patches/` は **使用されません**。Patch は `app.php` の隣にあり、`database/` 配下ではありません。

---

## Patch を作成

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

CLI はタイムスタンプ付きファイルを書き込みます。例:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

スタブ形状（匿名クラス）:

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

Platform 名前空間: `Pinoox\Patches`。

---

## PatchBase メソッド

| メソッド | 役割 |
|--------|------|
| `up()` | メインロジック（`run()` 経由で呼ばれる） |
| `down()` | `canRollback()` が true のときロールバック |
| `shouldRun()` | false の場合、Patch は **skipped** として記録 |
| `canRollback()` | ロールバックが許可されるか |
| `description()` | history 内の人間可読テキスト |
| `metadata()` | history に保存される追加 JSON |

---

## CLI コマンド

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**注意:** `patch:run` は **platform migrations** を先に実行し、次に選択パッケージの Patch を実行します。

エイリアス: `php pinoox patch` = `patch:run`。

---

## history テーブル

Migration と Patch は **`history`** テーブルを共有:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

成功した Patch は自動再実行されません。

---

## インストーラー

システムアプリ `com_pinoox_installer` は `SetupService` 経由でセットアップ中に Migration と Patch を実行します。

---

## ベストプラクティス

- 実行済み Patch を編集しない — 新規作成する。
- スキーマには Migration を使用し、Patch ではない。
- 冪等チェックで不要な作業をスキップするため `shouldRun()` を実装。
- `down()` が安全な場合のみロールバックを有効化。

---

## 関連ドキュメント

- [Migrations](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [CLI リファレンス](../start/cli-reference.md)

---

[← 索引に戻る](../README.md)
