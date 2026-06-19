# Pinx CLI（シングルアプリプロジェクト）

[← 索引に戻る](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** は **シングルアプリ** Pinoox プロジェクト向けの開発者 CLI です — マルチアプリ Manager に触れることなく、スキャフォールディング、実行、Migration、ビルド、`.pinx` パッケージの出荷ができます。

`pinoox/pincore` と `pinoox/app` テンプレート上に構築されています。プロジェクトルート **が** アプリです: 1 つの `app.php`、1 つのパッケージ、1 つのワークフロー。

> クラシックなマルチアプリプラットフォームインストールでは、代わりに [`php pinoox`](./cli-reference.md) を使用してください。

---

## クイックスタート

Pinx を一度インストールし、新しいアプリを作成して実行します。

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # com_my_shop を提案 — ウィザードで確認または編集
cd my-shop
cp .env.example .env          # Database を使う場合は DB_* を設定
pinx setup                    # platform + app を migrate、Seeder を実行
pinx dev                      # http://127.0.0.1:8000
```

`pinx` が見つからない場合は、Composer のグローバル `bin` を `PATH` に追加してください。

- Linux / macOS: `~/.composer/vendor/bin` または `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| ステップ | 内容 |
|------|--------------|
| `composer global require` | マシンに `pinx` コマンドをインストール |
| `pinx new my-shop` | `pinoox/app` からスキャフォールディング。ウィザードが 3 部構成パッケージ（例: `com_my_shop`）を提案 |
| `.env` | Database とプロジェクトパス — `.env.example` からコピー |
| `pinx setup` | ワンショット: platform migrations → app migrations → seeders |
| `pinx dev` | PHP 開発サーバー。フロントエンドスタックが設定されている場合は Vite も起動 |

パッケージ名は `com_{vendor}_{name}` に従います — 例: `com_acme_shop`、`ir_yekdo_app`。すでに空のフォルダ内にいる場合は `pinx new` の代わりに `pinx init` を使用してください。

**`setup` の前の任意チェック:** `pinx doctor` が PHP、レイアウト、env、DB、ビルドの準備状況を報告します。

---

## 代替: `composer create-project`

グローバルインストールなし — テンプレートにはプロジェクト内に `bin/pinx` が同梱されています。

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## シングルアプリの違い

クラシックな Pinoox インストールは `apps/` 配下に多くのアプリを保持し、実行時に 1 つを選択します。**シングルアプリ** はそれをフラット化します。

- プロジェクトルートの `app.php` にパッケージ ID と pinx 設定を保持
- `Controller/`、`Model/`、`routes/`、`theme/` はルートに配置 — `apps/{package}/` 内ではない
- `platform/` にローカルルーティングとランチャー設定（`.pinx` ビルドから除外）
- Pinx は常に **あなたの** アプリを対象 — パッケージピッカーや Manager UI なし

```
my-shop/                    ← プロジェクトルート = アプリルート
├── app.php                 ← package、version、pinx.sign、frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← dev ホスト + デプロイレイヤー（ローカルのみ）
├── bin/pinx                ← プロジェクトローカル CLI エントリ
└── vendor/pinoox/pincore   ← フレームワーク
```

---

## インストールオプション

| 場所 | 方法 | 使用タイミング |
|-------|-----|-------------|
| **グローバル** | `composer global require pinoox/pinx-cli` | 推奨 — どこからでも `pinx new` と `pinx init` |
| **プロジェクトごと** | `pinoox/app` の `bin/pinx` として同梱 | `composer create-project` 後 — グローバルインストール不要 |

```bash
pinx -v          # CLI バージョン（例: pinx-cli 1.1.7）
pinx list        # グループ化されたコマンド概要
pinx help setup  # 1 つのコマンドの詳細
```

---

## 日常のワークフロー

```bash
pinx dev                    # ローカルサーバー（app.php → frontend.stack 設定時は Vite も）
pinx dev --open             # 起動後にブラウザを開く
pinx dev --no-frontend      # PHP のみ

pinx migrate                # app migrations を実行（--platform で platform を先に実行）
pinx migrate:st             # migration ステータス
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # named actions 一覧（--validate、--json）
pinx test                   # アプリテストを実行（Pest）
```

**Frontend**（`theme/` が Vue/React + Vite を使用する場合）:

```bash
pinx fe:info                # スタック、npm スクリプト、パス
pinx fe:i                   # npm install
pinx fe:d                   # Vite 開発サーバー
pinx fe:b                   # 本番ビルド
pinx fe:sc --stack=vue      # スターターファイルをスキャフォールディング
```

**Dependencies:**

```bash
pinx deps:st                # Composer + npm ステータス
pinx deps:i                 # すべてインストール
pinx deps:up                # すべて更新
```

**Pinker**（ビルド Cache）:

```bash
pinx pinker:st              # cache vs source
pinx pinker:rb              # 再ビルド
pinx pinker:df              # diff
```

---

## 本番環境への出荷

フル Pinoox プラットフォーム（Manager → Applications）へのインストール用に `.pinx` パッケージをビルドします。

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # app.php のバージョンを上げてビルド
pinx release --sign         # app.php → pinx.sign にキーが設定されている場合に署名
```

`pinx build` は sensible なデフォルトを適用します（`vendor/`、`bin/`、`.env`、`platform/`、開発ツールを除外）。必要な場合のみ `app.php` で上書きしてください。

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor は構造化された診断を実行し、失敗時に修正コマンドを提案します。

| グループ | チェック内容 |
|-------|--------|
| **Project** | `app.php`、パッケージ ID、`platform/` レイアウト |
| **Runtime** | PHP バージョン（≥ 8.2）、拡張機能、書き込み可能なパス |
| **Dependencies** | Composer vendor、任意の Node/npm |
| **Environment** | `.env` の存在とキー変数 |
| **Database** | 接続（`--skip-db` でスキップ可能） |
| **Frontend** | テーマスタック、`package.json`（`--skip-frontend` でスキップ可能） |
| **Build** | エクスポート準備、icon、version フィールド |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # CI 向けレポート
pinx doctor --no-fixes      # 提案コマンドを非表示
```

---

## コマンドリファレンス

セクション別概要は `pinx list` を実行してください。短縮エイリアスは角括弧内に表示されます。

### Project

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `new` | — | `pinoox/app` からスキャフォールディング（ウィザードまたはフラグ） |
| `init` | — | 現在のディレクトリを初期化（`--force` で上書き） |
| `setup` | — | DB: platform + app を migrate、次に seed |
| `doctor` | `dr` | ヘルスチェック — `--json`、`--skip-db`、`--skip-frontend` |
| `info` | `inf` | `app.php` からメタデータを表示 |

### Development

| コマンド | 説明 |
|---------|-------------|
| `dev` | 開発サーバー。`frontend.stack` が vue/react の場合は Vite も |

### Database

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `migrate:run` | `migrate` | app migrations を実行（`--platform` で platform を先に実行） |
| `migrate:status` | `migrate:st` | Migration ステータス |
| `migrate:rollback` | `migrate:rb` | 最後のバッチをロールバック（`--ignore-fk`） |
| `migrate:create <name>` | `migrate:cr` | Migration ファイルを作成 |
| `migrate:platform` | `migrate:pl` | Platform migrations のみ |
| `seeder:run` | `seed` | Seeder を実行（`-c` クラス） |

### Patches

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `patch:run` | `patch` | 保留中の Patch を実行 |
| `patch:status` | `patch:st` | Patch ステータス |
| `patch:rollback` | `patch:rb` | 最後の Patch バッチをロールバック |

### Build & release

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `build` | `bld` | `.pinx` パッケージをビルド |
| `release` | `rel` | バージョンアップ + ビルド（`--bump`、`--sign`） |

### Scaffolding

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller、model、migration、patch、portal、form-request、seeder、test |

### Routes

| コマンド | 説明 |
|---------|-------------|
| `route:actions` / `routes` | Named actions 一覧（`--validate`、`--json`） |

### Dependencies

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Composer + npm ステータス |
| `deps:install` | `deps:i` | Dependencies をインストール |
| `deps:update` | `deps:up` | Dependencies を更新 |

### Frontend

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | テーマスタックと npm スクリプト |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | 本番ビルド |
| `fe:dev` | `fe:d` | Vite 開発サーバー |
| `fe:scaffold` | `fe:sc` | スターターファイル（`--stack=vue\|react\|twig`） |

### Schedule

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | `schedule.php` から cron タスク一覧 |
| `schedule:run` | `sched:run` | 期限到来タスクを実行（`--dry-run`） |

### Pinion（再開可能なアップロード）

`php pinoox pinion:*` に転送 — 一時的なチャンクアップロード session を管理。

| コマンド | 説明 |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

参照: [Pinion プロトコル](../advanced/pinion.md).

### Pinker

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Cache vs source |
| `pinker:rebuild` | `pinker:rb` | Cache を再ビルド |
| `pinker:diff` | `pinker:df` | 差分を表示 |
| `pinker:clear` | `pinker:cl` | Cache をクリア |
| `pinker:overrides` | `pinker:ov` | Override 一覧 |

### Quality & docs

| コマンド | 説明 |
|---------|-------------|
| `test` / `pest` | アプリテストを実行（`--unit`、`--feature`） |
| `api:docs` | REST API ドキュメント |
| `graphql:docs` | GraphQL スキーマドキュメント |

### Meta

| コマンド | エイリアス | 説明 |
|---------|---------|-------------|
| `list` | — | グループ化されたコマンド概要 |
| `version` | `ver` | CLI バージョン |

---

## アプリ検出

Pinx は現在の作業ディレクトリから上方向に走査し、有効なシングルアプリプロジェクトを見つけるまで続けます。

1. `app.php` が存在し、空でない `package` キーを持つ配列を返す
2. `composer.json` で `pinoox/pincore` が require されている、または `vendor/pinoox/pincore` が存在する

検出されたパッケージは環境変数で上書きできます。

| 変数 | 目的 |
|----------|---------|
| `PINX_PACKAGE` | CLI 対象パッケージを強制 |
| `PINOOX_DEV_APP` | `PINX_PACKAGE` のエイリアス |
| `PINX_DEV=1` | 開発モード（pincore に委譲する際 pinx が自動設定） |

---

## 要件

- **PHP** ≥ 8.2（`pinoox/pincore` が要求する拡張機能付き）
- **Composer** 2.x
- **Node.js** + npm — Vite/Vue/React フロントエンド使用時のみ
- **Database** — MySQL/MariaDB または `.env` で設定するもの（静的/Twig のみのアプリでは任意）

---

## 関連ドキュメント

- [Pinoox のインストール](./installing-pinoox.md)
- [Pinoox CLI リファレンス（マルチアプリ）](./cli-reference.md)
- [最初のアプリ](./your-first-app.md)
- [app.php マニフェスト](./app-manifest.md)

---

[← 索引に戻る](../README.md)
