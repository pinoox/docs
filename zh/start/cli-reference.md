# Pinoox CLI 参考

[← 返回索引](../README.md)

所有命令都在**项目根目录**下运行：

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

当命令需要包名而你未提供时，Pinoox 会显示一个交互式选择器。

> 对于**单应用**项目，请使用独立的 [Pinx CLI](./pinx-cli.md)（`pinx dev`、`pinx setup`、`pinx build` 等）。

---

## 常用别名

| 别名 | 命令 |
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

## 应用

| 命令 | 用途 |
|---------|---------|
| `app:create {package}` | 生成应用脚手架（`--simple`、`--stack`、`--profile`） |
| `app:list` | 列出应用 |
| `app:delete` | 删除应用 |
| `app:router set /path {package}` | URL 映射 |
| `app:domain` | 域名 → 应用映射 |
| `app:resolve` | 调试当前激活的应用 |

---

## 脚手架

| 命令 | 输出 |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest 类 |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest 文件 |
| `theme:frontend` | 前端工具链（Vue/React/Twig） |

---

## 数据库

| 命令 | 用途 |
|---------|---------|
| `migrate {package}` | 运行迁移（应用、`platform`、`pincore`） |
| `migrate:create` | 新建迁移文件 |
| `migrate:status` / `migrate:rollback` | 查看状态 / 回滚 |
| `seeder:run` | 运行填充器 |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [补丁（Patches）](../database/patches.md) |
| `query` | 原生 SQL（调试用） |

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

## Pinion（可恢复上传）

管理进行中的分块上传 session（临时目录 `storage/pinion`）：

| 命令 | 用途 |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

参见 [Pinion 协议](../advanced/pinion.md)。

---

## 缓存与 Pinker

| 命令 | 用途 |
|---------|---------|
| `cache:build` / `cache:clear` | 运行时缓存 |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | 重置 Pinker + 配置 |

---

## 计划任务

| 命令 | 用途 |
|---------|---------|
| `schedule:list` | 列出定时任务 |
| `schedule:run` | 运行到期任务 |

参见[计划任务（Schedule）](../advanced/schedule.md)。

---

## 路由

| 命令 | 用途 |
|---------|---------|
| `route:actions {package}` | 列出命名 Action |

---

## Pinx 打包

| 命令 | 用途 |
|---------|---------|
| `pinx:build` | 构建 `.pinx` 包 |
| `pinx:install` | 安装包 |
| `pinx:info` | 元数据 |
| `wizard:list` / `wizard:install` | 安装向导 |

---

## 开发

| 命令 | 用途 |
|---------|---------|
| `test` | Pest 测试 |
| `serve` | 内置开发服务器 |
| `log:view` / `log:clear` | 日志 |
| `deps` | 跨应用管理 Composer/npm |
| `version` / `mode:show` | 版本 / 运行模式 |

---

## 包名参数

| 取值 | 含义 |
|-------|---------|
| `com_my_shop` | 指定应用 |
| `platform` | 平台的迁移/补丁/填充器 |
| `pincore` | 框架核心 |
| `all` | 所有应用（缓存/pinker） |

---

## 相关文档

- [你的第一个应用](./your-first-app.md)
- [迁移（Migrations）](../database/migrations.md)
- [补丁（Patches）](../database/patches.md)

---

[← 返回索引](../README.md)
