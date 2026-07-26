# Migrations

[← 索引に戻る](../README.md)

Migration は Database の **スキーマ** 変更をバージョン管理します。Pinoox 3.x では、アプリファイルは `apps/{package}/database/migrations/`、コアファイルは `system/database/migrations/` にあります。

---

## Migration を作成

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

出力:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## ファイル構造

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

`$this->table('posts', $package)` が正しいアプリプレフィックスを適用します。

---

## Migration を実行

```bash
# アプリ migration
php pinoox migrate com_acme_blog

# コア migration
php pinoox migrate pincore

# プラットフォーム migration（pinx_* テーブル）
php pinoox migrate platform
```

---

## ステータスとロールバック

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## コア Migration（例）

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

コアテーブル: プレフィックス **`pincore_`**（または platform スコープの `pinx_`）。

---

## 名前空間

| 場所 | 名前空間 |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Core | `Pinoox\Database\migrations` |

---

## レガシーパス

Pinoox は旧 `apps/{package}/migrations/` フォルダも読みますが、**新規** ファイルは `database/migrations/` に作成されます。

---

## Migration vs Seed vs Patch

| 種類 | 目的 | コマンド |
|------|---------|---------|
| Migration | スキーマ（CREATE/ALTER） | `php pinoox migrate {package}` |
| Seeder | 初期データ | `php pinoox seeder:run {package}` |
| Patch | 一度限りのデータ変更 | `php pinoox patch:run {package}` |

Patch の完全ガイド: [Patches（データ更新）](../advanced/patches.md)。

---

## ベストプラクティス

- 1 Migration に 1 つの論理変更（1 テーブルまたは 1 ALTER）。
- 常に `down()` を書く。
- 実行済み Migration を編集しない — 新規作成する。
- コアテーブルへの外部キーは `$this->table(Table::FILE, 'platform')` を使用。

---

## 関連ドキュメント

- [Database はじめに](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [アプリ Database 設定（app.php）](../start/app-manifest.md)

---

[← 索引に戻る](../README.md)
