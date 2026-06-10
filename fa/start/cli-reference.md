# مرجع CLI پینوکس

[← بازگشت به فهرست](../../readme-fa.md)

همه دستورات از **ریشه پروژه** اجرا می‌شوند:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

اگر package لازم باشد و ندهید، لیست تعاملی نشان داده می‌شود.

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
| `seeder:create` | `database/seed/` |
| `test:create` | فایل Pest |
| `theme:frontend` | ابزار frontend (Vue/React/Twig) |

---

## دیتابیس

| دستور | کاربرد |
|--------|--------|
| `migrate {package}` | migration (اپ، `platform`, `pincore`) |
| `migrate:create` | فایل migration |
| `migrate:status` / `migrate:rollback` | وضعیت / برگشت |
| `seeder:run` | اجرای seeder |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patch](../database/patches.md) |
| `query` | SQL خام (debug) |

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
| `log:view` / `log:clear` | لاگ |
| `deps` | composer/npm در اپ‌ها |
| `version` / `mode:show` | نسخه / runtime mode |

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

- [ساخت اولین اپ](./your-first-app.md)
- [Migration — مهاجرت](../database/migrations.md)
- [Patch — پچ](../database/patches.md)

---

[← بازگشت به فهرست](../../readme-fa.md)
