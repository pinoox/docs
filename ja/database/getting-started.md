# Database はじめに

[← 索引に戻る](../README.md)

Pinoox 3.x の Database レイヤーは **Illuminate Database**（Eloquent + Query Builder）と **`Pinoox\Portal\Database\DB`** Portal 経由で提供されます。各アプリは `app.php` で接続を定義し、プラットフォーム認証情報はプロジェクト `.env` にあります。

---

## DB Portal

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // アクティブアプリ接続
DB::app('com_my_shop')->table('orders')->get();      // 特定アプリ接続
DB::core()->table('user')->get();                     // pincore テーブル
DB::tableName('orders');                             // プレフィックス付き物理名
```

---

## プラットフォームデフォルト

```php
// app.php
'database' => null,
```

Model とクエリはプロジェクトデフォルト接続（`.env` の `DB_CONNECTION`）を使用します。

---

## 名前付きプラットフォーム接続

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

Pinoox はプラットフォームブロックから `app_{package}_default` という名前の接続をクローンします。

---

## テーブルプレフィックス

### 共有 DB 上のアプリ（専用 database なし）

デフォルト: パッケージ名から導出される短いプレフィックス。

```php
'database' => null,
// com_pinoox_manager + table notifications → manager_notifications
```

### 明示的プレフィックス

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// または
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### 専用 DB — プレフィックスなし

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### コアテーブル

常にプレフィックス **`pincore_`**: `pincore_user`、`pincore_token`、`pincore_file`。

---

## 完全な専用 Database

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

複数接続:

```php
'database' => [
    'default' => 'primary',
    'connections' => [
        'primary' => ['driver' => 'sqlite', 'database' => ':memory:'],
        'analytics' => ['use' => 'mysql', 'prefix' => 'an_'],
    ],
],
```

```php
DB::app('com_my_shop', 'analytics')->table('events')->get();
```

---

## アプリ .env キー

| キー | マップ先 |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | 専用認証情報 |

アプリ `.env` では **`DB_CONNECTION` は使用しない** — 無視されます。

---

## database フォルダレイアウト

```text
apps/{package}/
├── patches/                 ← 一度限りのデータ Patch
└── database/
    migrations/
    seed/
```

---

## テーブル名の解決

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## ヒント

- ビジネスロジックは Model/Component に。Controller は薄く保つ
- Migration と Seed はアプリ `database/` フォルダのみ — pincore ではない
- Pinker は `database.use` と `database.prefix` を上書き可能

---

## 関連ドキュメント

- [Query Builder](./query-builder.md)
- [Migrations](./migrations.md)
- [Eloquent はじめに](../eloquent-orm/getting-started.md)
- [アプリ Database 設定（app.php）](../start/app-manifest.md)

---

[← 索引に戻る](../README.md)
