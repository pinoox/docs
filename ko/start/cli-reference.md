# Pinoox CLI 참조

[← 색인으로 돌아가기](../README.md)

모든 command는 **프로젝트 루트**에서 실행하세요:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

package가 필요한데 생략하면 Pinoox가 대화형 picker를 표시합니다.

> **단일 앱** 프로젝트는 독립 [Pinx CLI](./pinx-cli.md) (`pinx dev`, `pinx setup`, `pinx build`, …)를 사용하세요.

---

## 자주 쓰는 alias

| Alias | Command |
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

## Apps

| Command | Purpose |
|---------|---------|
| `app:create {package}` | 앱 스캐폴딩 (`--simple`, `--stack`, `--profile`) |
| `app:list` | 앱 목록 |
| `app:delete` | 앱 제거 |
| `app:router set /path {package}` | URL 매핑 |
| `app:domain` | Host → app map |
| `app:resolve` | 활성 앱 디버그 |

---

## Scaffolding

| Command | Output |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest class |
| `seeder:create` | `database/seeders/` |
| `test:create` | Pest file |
| `theme:frontend` | Frontend tooling (Vue/React/Twig) |

---

## Database

| Command | Purpose |
|---------|---------|
| `migrate {package}` | migration 실행 (app, `platform`, `pincore`) |
| `migrate:create` | 새 migration 파일 |
| `migrate:status` / `migrate:rollback` | 상태 / rollback |
| `seeder:run` | seeder 실행 |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | Raw SQL (debug) |

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

## Pinion (재개 가능 업로드)

진행 중인 청크 업로드 session 관리 (임시 저장: `storage/pinion`):

| 명령 | 용도 |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

문서: [Pinion 프로토콜](../advanced/pinion.md).

---

## Cache & Pinker

| Command | Purpose |
|---------|---------|
| `cache:build` / `cache:clear` | Runtime cache |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + config reset |

---

## Schedule

| Command | Purpose |
|---------|---------|
| `schedule:list` | cron task 목록 |
| `schedule:run` | due task 실행 |

[Schedule](../advanced/schedule.md) 참조.

---

## Router

| Command | Purpose |
|---------|---------|
| `route:actions {package}` | Named Actions 목록 |

---

## Pinx packaging

| Command | Purpose |
|---------|---------|
| `pinx:build` | `.pinx` package 빌드 |
| `pinx:install` | package 설치 |
| `pinx:info` | Metadata |
| `wizard:list` / `wizard:install` | Install wizard |

---

## Development

| Command | Purpose |
|---------|---------|
| `test` | Pest tests |
| `serve` | Built-in dev server |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm across apps |
| `version` / `mode:show` | Version / runtime mode |

---

## Package argument

| Value | Meaning |
|-------|---------|
| `com_my_shop` | Specific app |
| `platform` | Platform migrations/patches/seeders |
| `pincore` | Framework core |
| `all` | All apps (cache/pinker) |

---

## 관련 문서

- [첫 번째 앱 만들기](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← 색인으로 돌아가기](../README.md)
