# مرجع Pinoox CLI

[← العودة إلى الفهرس](../README.md)

شغّل كل الأوامر من **جذر المشروع**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

عندما تكون الحزمة (package) مطلوبة وتم حذفها، يعرض Pinoox منتقيًا تفاعليًا.

> لمشاريع **التطبيق الواحد**، استخدم [Pinx CLI](./pinx-cli.md) المستقل (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## الاختصارات الشائعة

| الاختصار | الأمر |
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

## التطبيقات

| الأمر | الغرض |
|---------|---------|
| `app:create {package}` | توليد هيكل تطبيق (`--simple`, `--stack`, `--profile`) |
| `app:list` | عرض قائمة التطبيقات |
| `app:delete` | حذف تطبيق |
| `app:router set /path {package}` | ربط الـ URL |
| `app:domain` | ربط المضيف (Host) ← التطبيق |
| `app:resolve` | تتبّع التطبيق النشط (debug) |

---

## توليد الهياكل (Scaffolding)

| الأمر | الناتج |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | صنف FormRequest |
| `seeder:create` | `database/seeders/` |
| `test:create` | ملف Pest |
| `theme:frontend` | أدوات الواجهة الأمامية (Vue/React/Twig) |

---

## قاعدة البيانات

| الأمر | الغرض |
|---------|---------|
| `migrate {package}` | تشغيل الترحيلات (التطبيق، `platform`، `pincore`) |
| `migrate:create` | ملف ترحيل جديد |
| `migrate:status` / `migrate:rollback` | الحالة / التراجع |
| `seeder:run` | تشغيل الـ seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [التصحيحات (Patches)](../database/patches.md) |
| `query` | SQL خام (للتتبع) |

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

## Pinion (الرفع القابل للاستئناف)

إدارة جلسات الرفع المجزأ قيد التنفيذ (تخزين مؤقت تحت `storage/pinion`):

| الأمر | الغرض |
|---------|---------|
| `pinion:list` | List sessions (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | Session detail + missing parts |
| `pinion:clean` | Remove expired sessions |
| `pinion:clean --abort={upload_id}` | Abort one session |

```bash
php pinoox pinion:list --status=pending
php pinoox pinion:info a1b2c3d4-...
```

مستندات: [بروتوكول Pinion](../advanced/pinion.md).

---

## الكاش و Pinker

| الأمر | الغرض |
|---------|---------|
| `cache:build` / `cache:clear` | كاش وقت التشغيل |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | إعادة تعيين Pinker + الإعدادات |

---

## الجدولة (Schedule)

| الأمر | الغرض |
|---------|---------|
| `schedule:list` | عرض مهام cron |
| `schedule:run` | تشغيل المهام المستحقة |

راجع [الجدولة](../advanced/schedule.md).

---

## الموجّه (Router)

| الأمر | الغرض |
|---------|---------|
| `route:actions {package}` | عرض الإجراءات المسماة (Named Actions) |

---

## حزم Pinx

| الأمر | الغرض |
|---------|---------|
| `pinx:build` | بناء حزمة `.pinx` |
| `pinx:install` | تثبيت الحزمة |
| `pinx:info` | البيانات الوصفية |
| `wizard:list` / `wizard:install` | معالج التثبيت |

---

## التطوير

| الأمر | الغرض |
|---------|---------|
| `test` | اختبارات Pest |
| `serve` | خادم التطوير المدمج |
| `log:view` / `log:clear` | السجلات |
| `deps` | Composer/npm عبر التطبيقات |
| `version` / `mode:show` | الإصدار / وضع التشغيل |

---

## وسيط الحزمة (Package argument)

| القيمة | المعنى |
|-------|---------|
| `com_my_shop` | تطبيق محدد |
| `platform` | ترحيلات/تصحيحات/seeders المنصة |
| `pincore` | نواة الإطار |
| `all` | كل التطبيقات (cache/pinker) |

---

## وثائق ذات صلة

- [تطبيقك الأول](./your-first-app.md)
- [الترحيلات (Migrations)](../database/migrations.md)
- [التصحيحات (Patches)](../database/patches.md)

---

[← العودة إلى الفهرس](../README.md)
