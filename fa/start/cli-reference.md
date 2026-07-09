# مرجع CLI پینوکس

[← بازگشت به فهرست](../README.md)

همه دستورات از **ریشه پروژه** اجرا می‌شوند:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

اگر package لازم باشد و ندهید، لیست تعاملی نشان داده می‌شود.

> برای پروژه‌های **تک‌اپ**، از ابزار مستقل [Pinx CLI](./pinx-cli.md) استفاده کنید (`pinx dev`، `pinx setup`، `pinx build` و…).

---

## aliasهای پرکاربرد

| alias | دستور |
|-------|--------|
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
| `pinion` | `pinion:list` |
| `make:permission` | `permission:create` |

---

## اپ

| دستور | کاربرد |
|--------|--------|
| `app:create {package}` | ساخت اپ (`--simple`, `--stack`, `--profile`) |
| `app:list` | لیست اپ‌ها |
| `app:delete` | حذف اپ |
| `app:router set /path {package}` | نگاشت URL |
| `app:domain` | تنظیم host → اپ |
| `app:resolve` | debug: کدام اپ handle می‌کند |

---

## Scaffolding

| دستور | خروجی |
|--------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest class |
| `seeder:create` | `database/seeders/` |
| `test:create` | فایل Pest |
| `theme:frontend` | Frontend tooling (Vue/React/Vite) — see [Frontend & Vite](../basic/frontend-vite.md) |

---

## دیتابیس

| دستور | کاربرد |
|--------|--------|
| `migrate {package}` | migration (اپ، `platform`, `pincore`) |
| `migrate:create` | فایل migration |
| `migrate:status` / `migrate:rollback` | وضعیت / برگشت |
| `seeder:run` | اجرای seeder |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patch](../database/patches.md) |
| `query` | SQL خام (debug؛ `--dry-run` فقط چاپ بدون اجرا) |

### مدیریت اتصال (`db:*`)

مشاهده و ذخیره اتصال‌های پلتفرم (Pinker `~database`) و بلوک `database` هر اپ.

| دستور | کاربرد |
|--------|--------|
| `db:list` | لیست اتصال پلتفرم یا تنظیمات DB اپ‌ها (`--all`, `--test`, `--json`) |
| `db:show {target}` | جزئیات برای `platform`، نام connection، یا package اپ |
| `db:test {target}` | تست اتصال؛ یا probe موقت با `--host`, `--database`, … |
| `db:create {name}` | افزودن connection پلتفرم (تعاملی یا `--set key=value`) |
| `db:update {target}` | به‌روزرسانی تنظیمات پلتفرم یا اپ |
| `db:prefix {package} {prefix}` | تغییر prefix جدول اپ (`--use` برای connection پلتفرم) |

```bash
php pinoox db:list --test
php pinoox db:show platform
php pinoox db:show com_my_shop --json
php pinoox db:test mysql
php pinoox db:prefix com_my_shop shop_
```

> CLI در **Pinker** می‌نویسد. اگر `.env` کلید `DB_*` داشته باشد، runtime ممکن است مقادیر را override کند (`env-over-pinker`).

جزئیات: [شروع دیتابیس](../database/getting-started.md).

---

## کاربر، نقش و دسترسی

دستورات scope مربوط به `transport.user` / access را رعایت می‌کنند (معمولاً `platform`). بدون `{package}` لیست تعاملی نشان داده می‌شود.

| دستور | کاربرد |
|--------|--------|
| `user:list` / `user:show` / `user:create` / `user:update` / `user:delete` | CRUD کاربر |
| `user:password` / `user:status` / `user:role` | رمز، وضعیت، نقش |
| `role:list` / `role:create` / … | CRUD نقش |
| `role:permission` | اتصال/جداسازی permission روی نقش |
| `permission:list` / `permission:create` / … | CRUD permission |

```bash
php pinoox user:list com_my_shop --status=active --json
php pinoox role:create com_my_shop --key=editor --name=Editor
php pinoox permission:create com_my_shop blog.posts.edit
php pinoox role:permission editor --attach=blog.posts.edit
```

مستندات: [مدیریت کاربر](../advanced/user-management.md)، [دسترسی و permission](../advanced/access-permissions.md).

---

## توکن

مدیریت ردیف‌های `TokenModel` برای scope تعریف‌شده در `transport.session_token`.

| دستور | کاربرد |
|--------|--------|
| `token:list` / `token:show` | مشاهده توکن‌ها (کلید در list ماسک می‌شود) |
| `token:create` | ساخت توکن برای کاربر (`--user`, `--lifetime`, `--unit`) |
| `token:update` / `token:delete` | ویرایش یا حذف یک توکن |
| `token:revoke-user` | لغو همه توکن‌های یک کاربر (مثل `Auth::revokeSessions`) |
| `token:purge` | حذف توکن‌های منقضی |

```bash
php pinoox token:list platform
php pinoox token:create com_my_shop --user=1 --lifetime=30 --unit=day
php pinoox token:revoke-user 1
```

مستندات: [مدیریت توکن](../advanced/token-management.md).

---

## فایل

مدیریت metadata و storage برای scope `FileModel` (`transport.file_storage`).

| دستور | کاربرد |
|--------|--------|
| `file:list` / `file:show` | لیست یا جزئیات (وضعیت storage: `present` / `missing`) |
| `file:update` | metadata، access، لینک |
| `file:delete` | حذف ردیف DB، storage، یا هر دو (`--db-only`, `--storage-only`, `--force`) |
| `file:purge` | پاکسازی گروهی |

```bash
php pinoox file:list com_my_shop
php pinoox file:show 12
php pinoox file:delete 12 --storage-only --force
```

مستندات: [مدیریت فایل](../advanced/file-management.md).

---

## Pinion (آپلود تکه‌ای)

مدیریت sessionهای آپلود در حال انجام (فضای موقت: `storage/pinion`):

| دستور | کاربرد |
|--------|--------|
| `pinion:list` | لیست sessionها (`--status=pending`, `--json`) |
| `pinion:info {upload_id}` | جزئیات و partهای باقی‌مانده |
| `pinion:clean` | حذف sessionهای منقضی |
| `pinion:clean --abort={upload_id}` | لغو یک session |

```bash
php pinoox pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

مستندات: [پروتکل Pinion](../advanced/pinion.md).

---

## Pinroll (انتشار و دیپلوی)

ساخت پکیج و دیپلوی به targetهای پیکربندی‌شده. نیاز به `pinoox/pinroll` و config از `pinroll:init`.

| دستور | نام جایگزین | کاربرد |
|--------|-------------|--------|
| `pinroll:init` | — | ساخت config؛ `-w` ویزارد |
| `pinroll:vendor` | `pinroll:vendor:pack`، `pinroll:pack:vendor` | خروجی `vendor/` برای نصب هاست یا آپدیت هسته |
| `pinroll:gate` | `pinroll:gate:init` | ساخت PinGate؛ آپلود FTP به‌صورت پیش‌فرض (`-z`، `--no-upload`، `--rotate`) |
| `pinroll:check` | — | بررسی target / PinGate |
| `pinroll:push` | `pinroll:deploy`، `pinroll:prod` | ساخت و ارسال (`-a` = apply از طریق PinGate) |
| `pinroll:apply` | — | اعمال release روی target (یا `--local` روی هاست) |
| `pinroll:rollback` | — | rollback از طریق PinGate |
| `pinroll:cleanup` | `pinroll:prune` | پاکسازی archiveهای قدیمی |
| `pinroll:build` | — | فقط build |
| `pinroll:status` | — | وضعیت rollout |
| `pinroll:history` | — | تاریخچه دیپلوی |
| `pinroll:pull` | `pinroll:poll` | دریافت manifest جدیدتر از release server |

```bash
php pinoox pinroll:init -w
php pinoox pinroll:vendor
php pinoox pinroll:gate
php pinoox pinroll:push production -a --package=com_pinoox_shop
```

مستندات: [راهنمای Pinroll](../deploy/pinroll.md).

---

## Cache و Pinker

| دستور | کاربرد |
|--------|--------|
| `cache:build` / `cache:clear` | cache runtime |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | reset Pinker + config |

---

## Schedule

| دستور | کاربرد |
|--------|--------|
| `schedule:list` | لیست cron task |
| `schedule:run` | اجرای taskهای due |

جزئیات: [Schedule](../advanced/schedule.md).

---

## Router

| دستور | کاربرد |
|--------|--------|
| `route:actions {package}` | لیست Named Actionها |

---

## Package (Pinx)

| دستور | کاربرد |
|--------|--------|
| `pinx:build` | ساخت `.pinx` |
| `pinx:install` | نصب package |
| `pinx:info` | metadata |
| `wizard:list` / `wizard:install` | wizard نصب |

---

## توسعه

| دستور | کاربرد |
|--------|--------|
| `test` | Pest tests |
| `serve` | سرور dev داخلی |
| `theme:frontend` / `fe` | dev، build و watch با Vite — [فرانت‌اند و Vite](../basic/frontend-vite.md) |
| `log:view` / `log:clear` | لاگ |
| `deps` | composer/npm در اپ‌ها |
| `version` / `mode:show` | نسخه / runtime mode |

### `theme:frontend` (`fe`)

```bash
php pinoox fe info com_my_shop
php pinoox fe install com_my_shop
php pinoox fe dev com_my_shop              # PHP serve + Vite HMR (تا آماده شدن Vite)
php pinoox dev com_my_shop                 # میانبر fe dev
php pinoox fe dev com_my_shop --no-serve   # فقط Vite (MAMP / PHP خارجی)
php pinoox fe dev com_my_shop --fix-vite   # اتصال @pinooxhq/vite-plugin به vite.config.js
php pinoox fe dev:apps                     # چند اپ: یک serve + Vite برای هر package
php pinoox fe dev:apps --apps=com_pinoox_manager,com_pinoox_welcome
php pinoox fe build com_my_shop
php pinoox fe watch com_my_shop
php pinoox fe scaffold com_my_shop vue
php pinoox serve --app=com_my_shop@/manager # فقط manifest (PINOOX_VITE_HMR=0)
```

`fe dev` مقدار `PINOOX_VITE_HMR=1` را تنظیم می‌کند، URLهای `VITE_*` را از روتر اپ resolve می‌کند و مقادیر خالی را در runtime inject می‌کند. **URL PHP** چاپ‌شده در ترمینال را باز کنید — نه port Vite. `php pinoox serve` همیشه از دارایی‌های build‌شده manifest استفاده می‌کند. [فرانت‌اند و Vite](../basic/frontend-vite.md) و [@pinooxhq/vite-plugin](../basic/vite-plugin.md) را ببینید.

---

## package در دستورات

| مقدار | معنی |
|--------|------|
| `com_my_shop` | اپ مشخص |
| `platform` | migration/patch/seeder پلتفرم |
| `pincore` | هسته فریمورک |
| `all` | همه اپ‌ها (cache/pinker) |

---

## مستندات مرتبط

- [فرانت‌اند و Vite](../basic/frontend-vite.md)
- [ساخت اولین اپ](./your-first-app.md)
- [Migration — مهاجرت](../database/migrations.md)
- [Patch — پچ](../database/patches.md)
- [مدیریت کاربر](../advanced/user-management.md)
- [دسترسی و permission](../advanced/access-permissions.md)
- [مدیریت توکن](../advanced/token-management.md)
- [مدیریت فایل](../advanced/file-management.md)

---

[← بازگشت به فهرست](../README.md)
