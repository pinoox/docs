# テストデータ — Seeders

[← 索引に戻る](../README.md)

Pinoox 3.x の CLI には **Model Factory**（Laravel スタイル）は含まれていません。初期データと開発データの推奨アプローチは、`apps/{package}/database/seeders/` の **Seeder** と `SeederBase` です。

---

## Seeder を作成

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## 構造

```php
<?php
namespace App\com_acme_blog\database\seeders;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Seeder\SeederBase;
use Pinoox\Portal\Hash;

return new class extends SeederBase
{
    public function run(): void
    {
        PostModel::insert([
            [
                'user_id' => 1,
                'title' => 'First post',
                'body' => 'Sample content',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Second post',
                'body' => '...',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
```

---

## 別 Seeder を呼び出し

```php
public function run(): void
{
    $this->call([
        'RoleSeeder',
        'UserSeeder',
    ]);

    // ユーザー後の依存データ
    PostModel::factory(); // ❌ 利用不可 — insert または create を手動で
}
```

---

## Model で create

```php
for ($i = 1; $i <= 20; $i++) {
    PostModel::create([
        'user_id' => 1,
        'title' => "Post #{$i}",
        'body' => 'Lorem ipsum',
        'status' => $i % 2 ? 'published' : 'draft',
    ]);
}
```

---

## Seeder を実行

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
php pinoox seeder:run platform
```

`-c` matches the **file basename** (e.g. `PostSeeder`). App install does **not** auto-run seeders.

---

## Call Seeders From Code

Use `Pinoox\Portal\Database\Seeder`, or `$this->seed()` / `$this->seedAll()` in migrations and patches.

```php
use Pinoox\Portal\Database\Seeder;

Seeder::run('PostSeeder');
Seeder::run('PostSeeder', 'com_acme_blog');
Seeder::run(['RoleSeeder', 'PostSeeder']);
Seeder::runAll();
Seeder::runAll('platform');
```

```php
// migration / patch
$this->seed('GatewaySeeder');
$this->seedAll();
```

```php
// from another seeder
$this->call(['RoleSeeder', 'UserSeeder']);
```

See English docs: [Factories and seeders](../../en/eloquent-orm/factories.md#call-seeders-from-code).

---

## 推奨順序

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## 本番環境での Seeder

- **必須** データのみ（ロール、デフォルト設定）。
- 偽/開発データは `APP_ENV` でガード:

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // サンプルデータ
}
```

---

## Seeder vs Patch

| Seeder | Patch |
|--------|-------|
| 初期 / サンプルデータ | 既存データの一度限り修正 |
| `seeder:run` — 注意して繰り返し可能 | `patch:run` — 1 回追跡 |

---

## ヒント

- 冪等 Seeder を書く（盲目的 `insert` より `firstOrCreate`）。
- Seeder に実際の認証情報をコミットしない。
- ユニットテストには Pest フィクスチャまたは `:memory:` sqlite を使用。

---

## 関連ドキュメント

- [Migrations](../database/migrations.md)
- [Eloquent はじめに](./getting-started.md)
- [アプリ Database 設定（app.php）](../start/app-manifest.md)

---

[← 索引に戻る](../README.md)
