# Pinoox CLI リファレンス

[← 索引に戻る](../README.md)

すべてのコマンドは **プロジェクトルート** から実行します。

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

パッケージが必要で省略された場合、Pinoox は対話式ピッカーを表示します。

> **シングルアプリ** プロジェクトでは、スタンドアロンの [Pinx CLI](./pinx-cli.md)（`pinx dev`、`pinx setup`、`pinx build` など）を使用してください。

---

## よく使うエイリアス

| エイリアス | コマンド |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## アプリ

| コマンド | 目的 |
|---------|---------|
| `app:create {package}` | アプリをスキャフォールディング（`--simple`、`--stack`、`--profile`） |
| `app:list` | アプリ一覧 |
| `app:delete` | アプリを削除 |
| `app:router set /path {package}` | URL マッピング |
| `app:domain` | ホスト → アプリ マップ |
| `app:resolve` | アクティブなアプリをデバッグ |

---

## スキャフォールディング

| コマンド | 出力 |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest クラス |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest ファイル |
| `theme:frontend` | フロントエンドツール（Vue/React/Twig） |

---

## Database

| コマンド | 目的 |
|---------|---------|
| `migrate {package}` | Migration を実行（app、`platform`、`pincore`） |
| `migrate:create` | 新しい Migration ファイル |
| `migrate:status` / `migrate:rollback` | ステータス / ロールバック |
| `seeder:run` | Seeder を実行 |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | 生 SQL（デバッグ） |

---

## Cache と Pinker

| コマンド | 目的 |
|---------|---------|
| `cache:build` / `cache:clear` | ランタイム Cache |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + 設定をリセット |

---

## Schedule

| コマンド | 目的 |
|---------|---------|
| `schedule:list` | cron タスク一覧 |
| `schedule:run` | 期限到来タスクを実行 |

[Schedule](../advanced/schedule.md) を参照してください。

---

## Router

| コマンド | 目的 |
|---------|---------|
| `route:actions {package}` | Named Actions 一覧 |

---

## Pinx パッケージング

| コマンド | 目的 |
|---------|---------|
| `pinx:build` | `.pinx` パッケージをビルド |
| `pinx:install` | パッケージをインストール |
| `pinx:info` | メタデータ |
| `wizard:list` / `wizard:install` | インストールウィザード |

---

## 開発

| コマンド | 目的 |
|---------|---------|
| `test` | Pest テスト |
| `serve` | 組み込み開発サーバー |
| `log:view` / `log:clear` | ログ |
| `deps` | アプリ全体の Composer/npm |
| `version` / `mode:show` | バージョン / ランタイムモード |

---

## パッケージ引数

| 値 | 意味 |
|-------|---------|
| `com_my_shop` | 特定のアプリ |
| `platform` | Platform Migrations/Patches/Seeders |
| `pincore` | フレームワークコア |
| `all` | すべてのアプリ（cache/pinker） |

---

## 関連ドキュメント

- [最初のアプリ](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← 索引に戻る](../README.md)
