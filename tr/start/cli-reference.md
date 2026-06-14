# Pinoox CLI referansı

[← Dizine dön](../README.md)

Her komutu **proje kökünden** çalıştırın:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Paket gerekli olduğunda ve belirtilmediğinde Pinoox etkileşimli bir seçici gösterir.

> **Tek uygulamalı** projeler için bağımsız [Pinx CLI](./pinx-cli.md) kullanın (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Yaygın takma adlar

| Takma ad | Komut |
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
| `databases` | `db:list` |
| `make:permission` | `permission:create` |

---

## Uygulamalar

| Komut | Amaç |
|---------|---------|
| `app:create {package}` | Uygulama iskeleti (`--simple`, `--stack`, `--profile`) |
| `app:list` | Uygulamaları listele |
| `app:delete` | Uygulamayı kaldır |
| `app:router set /path {package}` | URL eşlemesi |
| `app:domain` | Host → uygulama haritası |
| `app:resolve` | Aktif uygulamayı debug et |

---

## İskelet oluşturma

| Komut | Çıktı |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest sınıfı |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest dosyası |
| `theme:frontend` | Frontend araçları (Vue/React/Twig) |

---

## Veritabanı

| Komut | Amaç |
|---------|---------|
| `migrate {package}` | Migration'ları çalıştır (uygulama, `platform`, `pincore`) |
| `migrate:create` | Yeni migration dosyası |
| `migrate:status` / `migrate:rollback` | Durum / geri alma |
| `seeder:run` | Seeder'ları çalıştır |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patch'ler](../database/patches.md) |
| `query` | Ham SQL (debug) |

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

---

## Önbellek ve Pinker

| Komut | Amaç |
|---------|---------|
| `cache:build` / `cache:clear` | Runtime önbelleği |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + config sıfırla |

---

## Zamanlama

| Komut | Amaç |
|---------|---------|
| `schedule:list` | Cron görevlerini listele |
| `schedule:run` | Vadesi gelen görevleri çalıştır |

Bkz. [Zamanlama](../advanced/schedule.md).

---

## Router

| Komut | Amaç |
|---------|---------|
| `route:actions {package}` | Named Action'ları listele |

---

## Pinx paketleme

| Komut | Amaç |
|---------|---------|
| `pinx:build` | `.pinx` paketi oluştur |
| `pinx:install` | Paket kur |
| `pinx:info` | Meta veri |
| `wizard:list` / `wizard:install` | Kurulum sihirbazı |

---

## Geliştirme

| Komut | Amaç |
|---------|---------|
| `test` | Pest testleri |
| `serve` | Yerleşik dev sunucusu |
| `log:view` / `log:clear` | Loglar |
| `deps` | Uygulamalar genelinde Composer/npm |
| `version` / `mode:show` | Sürüm / runtime modu |

---

## Paket argümanı

| Değer | Anlam |
|-------|---------|
| `com_my_shop` | Belirli uygulama |
| `platform` | Platform migration'ları/patch'leri/seeder'ları |
| `pincore` | Framework çekirdeği |
| `all` | Tüm uygulamalar (önbellek/pinker) |

---

## İlgili dokümantasyon

- [İlk uygulamanız](./your-first-app.md)
- [Migration'lar](../database/migrations.md)
- [Patch'ler](../database/patches.md)

---

[← Dizine dön](../README.md)
