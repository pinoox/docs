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
| `users` | `user:list` |
| `roles` | `role:list` |
| `permissions` | `permission:list` |
| `tokens` | `token:list` |
| `files` | `file:list` |
| `pinion` | `pinion:list` |
| `databases` | `db:list` |
| `make:permission` | `permission:create` |

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

### Connection management (`db:*`)

Inspect and persist platform connections (Pinker `~database`) and per-app `database` blocks.

| Command | Purpose |
|---------|---------|
| `db:list` | List platform connections or app DB settings (`--all`, `--test`, `--json`) |
| `db:show {target}` | Connection details for `platform`, a connection name, or an app package |
| `db:test {target}` | Test connectivity; ad-hoc probe with `--host`, `--database`, `--username`, … |
| `db:create {name}` | Add a platform connection (interactive or `--set key=value`) |
| `db:update {target}` | Update platform or app database settings |
| `db:prefix {package} {prefix}` | Change app table prefix (`--use` to pick platform connection) |

```bash
php pinoox db:list --test
php pinoox db:show platform
php pinoox db:show com_my_shop --json
php pinoox db:test mysql
php pinoox db:prefix com_my_shop shop_
```

> CLI writes to **Pinker**. Runtime may still override values when `.env` defines `DB_*` keys (`env-over-pinker`).

See [Database getting started](../database/getting-started.md).

---

## Users, roles & permissions

Commands respect `transport.user` / access scope (usually `platform`). Omit `{package}` to pick from the interactive list.

| Command | Purpose |
|---------|---------|
| `user:list` / `user:show` / `user:create` / `user:update` / `user:delete` | User CRUD |
| `user:password` / `user:status` / `user:role` | Password, status, role assignment |
| `role:list` / `role:create` / `role:show` / `role:update` / `role:delete` | Role CRUD |
| `role:permission` | Attach or detach permissions on a role |
| `permission:list` / `permission:create` / `permission:show` / `permission:delete` | Permission CRUD |

```bash
php pinoox user:list com_my_shop --status=active --json
php pinoox role:create com_my_shop --key=editor --name=Editor
php pinoox permission:create com_my_shop blog.posts.edit
php pinoox role:permission editor --attach=blog.posts.edit
```

See [User management](../advanced/user-management.md) and [Access & permissions](../advanced/access-permissions.md).

---

## Tokens

Manage `TokenModel` rows for the transport scope (`transport.session_token` in `app.php`).

| Command | Purpose |
|---------|---------|
| `token:list` / `token:show` | Inspect tokens (keys masked in list output) |
| `token:create` | Create token for a user (`--user`, `--lifetime`, `--unit`) |
| `token:update` / `token:delete` | Update metadata or remove one token |
| `token:revoke-user` | Revoke all tokens for a user (like `Auth::revokeSessions`) |
| `token:purge` | Delete expired tokens |

```bash
php pinoox token:list platform
php pinoox token:create com_my_shop --user=1 --lifetime=30 --unit=day
php pinoox token:revoke-user 1
```

See [Token management](../advanced/token-management.md).

---

## Files

Manage upload metadata and storage for the `FileModel` scope (`transport.file_storage`).

| Command | Purpose |
|---------|---------|
| `file:list` / `file:show` | List or inspect records (shows storage `present` / `missing`) |
| `file:update` | Update metadata, access, or links |
| `file:delete` | Remove DB row, storage, or both (`--db-only`, `--storage-only`, `--force`) |
| `file:purge` | Bulk cleanup of orphaned or old files |

```bash
php pinoox file:list com_my_shop
php pinoox file:show 12
php pinoox file:delete 12 --storage-only --force
```

See [File management](../advanced/file-management.md).

---

## Pinion（再開可能なアップロード）

進行中のチャンクアップロード session を管理（一時保存: `storage/pinion`）:

| コマンド | 用途 |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

参照: [Pinion プロトコル](../advanced/pinion.md).

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
