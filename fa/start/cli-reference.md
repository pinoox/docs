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
| `mg:create` / `mg:make` / `make:migration` | `migrate:create` |
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
| `app:reset {package}` | ریست دیتای اپ (فولدر می‌ماند)، سپس migrate + patch + lifecycle نصب |
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
| `lifecycle:create` | `lifecycle.php` (نصب/آپدیت/حذف/ریست) |
| `theme:frontend` | Frontend tooling (Vue/React/Vite) — see [Frontend & Vite](../basic/frontend-vite.md) |

---

## دیتابیس

| دستور | کاربرد |
|--------|--------|
| `migrate {package}` | اجرای migration (اپ یا `platform`) |
| `migrate:create` | فایل migration (`--create`، `--table`) |
| `migrate:drop` | حذف سخت جداول پکیج و پاک کردن تاریخچه |
| `migrate:status` / `migrate:rollback` | وضعیت / برگشت |
| `seeder:run` | اجرای seeder (`-c` نام فایل) |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patch](../advanced/patches.md) |
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
| `user:login` / `user:logout` | صدور یا پایان توکن؛ با `--force` نوشتن/پاک‌کردن `PINOOX_LOGIN_TOKEN` |
| `role:list` / `role:create` / … | CRUD نقش |
| `role:permission` | اتصال/جداسازی permission روی نقش |
| `permission:list` / `permission:create` / … | CRUD permission |

```bash
php pinoox user:list com_my_shop --status=active --json
php pinoox user:login com_my_shop --id=1 --force
php pinoox user:logout --force
php pinoox role:create com_my_shop --key=editor --name=Editor
php pinoox permission:create com_my_shop blog.posts.edit
php pinoox role:permission editor --attach=blog.posts.edit
```

مستندات: [مدیریت کاربر](../advanced/user-management.md) (شامل `PINOOX_LOGIN` / `PINOOX_LOGIN_TOKEN`)، [دسترسی و permission](../advanced/access-permissions.md).

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

ساخت پکیج و دیپلوی به هاست‌های پیکربندی‌شده. نیاز به `pinoox/pinroll` و config از `pinroll:init`.

| دستور | کاربرد |
|--------|--------|
| `pinroll:init` | ساخت `.pinoox/pinroll.config.php` |
| `pinroll:provision` | نصب اولیه هاست خالی (PinGate + platform.zip + setup) |
| `pinroll:connect` | راه‌اندازی / بررسی هاست (`--reset` برای تکرار) |
| `pinroll:apps` | تنظیم `hosts.*.apps` |
| `pinroll:vendor` | `vendor.zip` production (`--push` به هاست) |
| `pinroll:gate` | ساخت / آپلود PinGate |
| `pinroll:check` | بررسی هاست / PinGate |
| `pinroll:push` | فقط ساخت و آپلود |
| `pinroll:setup` | بعد از دیپلوی: migrate + patch (`--seed`، `--config`، `--dry-run`) |
| `pinroll:install` | نصب release آماده‌شده روی هاست |
| `pinroll:deploy` | push + install؛ `--full` = پلتفرم + همه اپ‌ها |
| `pinroll:rollback` | rollback از PinGate یا آرشیو لوکال |
| `pinroll:cleanup` | هرس آرشیوهای قدیمی (`--local`، `--dry-run`) |
| `pinroll:build` | فقط build |
| `pinroll:status` | وضعیت rollout |
| `pinroll:history` | تاریخچه دیپلوی |
| `pinroll:pull` | دریافت manifest جدیدتر از release server |

```bash
php pinoox pinroll:init
php pinoox pinroll:provision          # هاست خالی
php pinoox pinroll:connect            # سایت موجود
php pinoox pinroll:deploy --full
php pinoox pinroll:setup
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
| `pinx:install` | نصب package (`--skip-lifecycle` برای رد `lifecycle.php`) |
| `pinx:uninstall` | حذف اپ/تم (`--skip-lifecycle`) |
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
| `deps` | composer/npm در اپ‌ها — [CLI وابستگی‌ها](./deps-cli.md) |
| `version` / `mode:show` | نسخه / runtime mode |

### `theme:frontend` (`fe`)

target یک **package** (`com_my_shop`) یا **پوشه تم** (`spark`) است. ترتیب پیشنهادی: `{target} {action}`.

```bash
php pinoox fe spark info
php pinoox fe spark install
php pinoox fe spark dev                    # PHP serve + Vite HMR (تا آماده شدن Vite)
php pinoox dev spark                       # میانبر fe spark dev
php pinoox dev spark --domain=pinoox.test  # hostname محلی (hosts خودکار)
php pinoox fe spark dev --no-serve         # فقط Vite (MAMP / PHP خارجی)
php pinoox fe spark dev --theme=panel      # یک theme context
php pinoox fe spark dev --theme=all        # همه contextهای Vite در اپ
php pinoox fe spark dev --fix-vite         # اتصال @pinooxhq/vite-plugin به vite.config.js
php pinoox dev platform                    # روتر کامل پلتفرم
php pinoox fe dev:apps                     # چند اپ: یک serve + Vite برای هر package
php pinoox fe dev:apps --apps=com_pinoox_manager,com_pinoox_welcome
php pinoox fe spark build
php pinoox fe:build                        # wizard تم (همان fe build)
php pinoox fe spark watch
php pinoox fe spark scaffold vue
php pinoox serve --app=com_my_shop@/manager # فقط manifest (PINOOX_VITE_HMR=0)
```

`fe dev` مقدار `PINOOX_VITE_HMR=1` را تنظیم می‌کند، URLهای `VITE_*` را از روتر اپ resolve می‌کند و مقادیر خالی را در runtime inject می‌کند. HMR با **`.pinoox/dev.json`** (نوشته‌شده توسط Vite) سیگنال می‌شود. **URL PHP** چاپ‌شده در ترمینال را باز کنید — نه port Vite. `php pinoox serve` همیشه از دارایی‌های build‌شده manifest استفاده می‌کند. [فرانت‌اند و Vite](../basic/frontend-vite.md) و [@pinooxhq/vite-plugin](../basic/vite-plugin.md) را ببینید.

### `deps`

```bash
php pinoox deps status all
php pinoox deps install all
php pinoox deps install com_my_shop --npm-only
php pinoox deps install platform --plain --no-interaction
```

Composer (پلتفرم + هر اپ) و npm (تم) را نصب/به‌روز می‌کند. Scopeها: `all`، `platform`، یا نام پکیج. گزینه‌ها و عیب‌یابی: [CLI وابستگی‌ها (`deps`)](./deps-cli.md).

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
- [Patch — پچ](../advanced/patches.md)
- [مدیریت کاربر](../advanced/user-management.md)
- [دسترسی و permission](../advanced/access-permissions.md)
- [مدیریت توکن](../advanced/token-management.md)
- [مدیریت فایل](../advanced/file-management.md)

---

[← بازگشت به فهرست](../README.md)
