# CLI وابستگی‌ها (`deps`)

[← بازگشت به فهرست](../README.md)

نصب، به‌روزرسانی و بررسی وابستگی‌های **Composer** (PHP) و **npm** (فرانت تم) در کل پروژه پینوکس با یک دستور.

از ریشه پروژه:

```bash
php pinoox deps status all
php pinoox deps install all
php pinoox deps install com_pinoox_manager
```

در پروژه‌های تک‌اپ Pinx همان کارها با این aliasها:

```bash
pinx deps:st
pinx deps:i
pinx deps:up
```

---

## چه چیزی را مدیریت می‌کند

| مانیفست | مسیر معمول | زمان در نظر گرفتن |
|---------|------------|-------------------|
| `composer.json` (پلتفرم) | ریشه پروژه | وجود `vendor/autoload.php` |
| `composer.json` (اپ) | `apps/{package}/composer.json` | وجود `apps/{package}/vendor/autoload.php` |
| `package.json` (تم) | `apps/{package}/theme/{theme}/package.json` | وجود `node_modules/` در آن تم |

اهداف به‌صورت خودکار کشف می‌شوند. شما **scope** را انتخاب می‌کنید (کل پروژه، فقط پلتفرم، یا یک اپ).

---

## اکشن‌ها

| اکشن | کاربرد |
|------|--------|
| `status` | جدول موجودی — نصب‌شده در برابر ناقص |
| `install` | `composer install` و `npm ci` / `npm install` |
| `update` | `composer update` و `npm update` |

---

## Scopeها

| Scope | اهداف |
|-------|--------|
| `all` | composer ریشه + composer هر اپ + `package.json` تم فعال هر اپ |
| `platform` | فقط `composer.json` ریشه |
| `com_my_shop` | `composer.json` آن اپ + تم فعال از `app.php` |

حالت تعاملی: آرگومان scope را ندهید و از جدول شماره‌دار انتخاب کنید.

---

## رابط گام‌به‌گام

`install` و `update` به‌صورت **workflow** اجرا می‌شوند:

1. **Header** — اکشن، scope، تعداد هدف  
2. **برنامه اجرا** — جدول مراحل (pending → running → done)  
3. **نوار پیشرفت** — شمارنده مراحل  
4. **پنل مرحله فعال** — مسیر، مانیفست، خروجی زنده فیلترشده  
5. **خلاصه جمع‌شده** — هر مرحله تمام‌شده یک خط `✔ done`  
6. **خلاصه نهایی** — جدول زمان و وضعیت خروج  

خروجی زنده هنگام اجرای Composer/npm استریم می‌شود. به‌صورت پیش‌فرض فقط خطوط معنادار نشان داده می‌شوند. برای لاگ کامل:

```bash
php pinoox deps install all -v
php pinoox deps install all -vv
```

برای CI یا اسکریپت بدون پنل:

```bash
php pinoox deps install platform --plain --no-interaction
```

---

## گزینه‌ها

| گزینه | توضیح |
|-------|--------|
| `--composer-only` | رد کردن اهداف npm |
| `--npm-only` | رد کردن اهداف Composer |
| `--theme=spark` | پوشه تم برای یک اپ (پیش‌فرض: `app.php` → `theme`) |
| `--all-themes` | همه تم‌های زیر `apps/{package}/theme/` که `package.json` دارند |
| `--production` | Composer: `--no-dev` + autoloader بهینه‌شده |
| `--no-ci` | npm: همیشه `npm install` (حتی با lockfile، `npm ci` را نزن) |
| `--plain` | بخش‌های ساده، بدون پنل مرحله |
| `--continue-on-error` | بعد از شکست، بقیه اهداف را ادامه بده |
| `-v` / `-vv` | خروجی زنده پرجزئیات از ابزارها |

---

## مثال‌ها

### راه‌اندازی اولیه پروژه

```bash
php pinoox deps install platform
php pinoox deps install all --npm-only
```

### یک اپ قبل از build

```bash
php pinoox deps install com_pinoox_manager
php pinoox theme:frontend build com_pinoox_manager --no-install
```

`theme:frontend build` وقتی وابستگی‌ها از قبل با `deps` نصب شده‌اند می‌تواند npm را رد کند.

### پکیج تولید (بدون وابستگی‌های dev PHP)

```bash
php pinoox deps install com_my_shop --production --composer-only
```

### همه تم‌های یک اپ

```bash
php pinoox deps install com_pinoox_manager --all-themes --npm-only
```

### بررسی کمبودها

```bash
php pinoox deps status all
```

---

## نکات رفتار

- **باینری Composer** — از `COMPOSER_BIN`، `composer.phar` پروژه، یا `composer` در `PATH` (همان منطق `pinx:build`).
- **npm در ویندوز** — به‌صورت خودکار `npm.cmd`.
- **npm ci** — وقتی `package-lock.json` هست (مگر `--no-ci`). اگر `ci` شکست بخورد به `npm install` برمی‌گردد.
- **شکست** — روی اولین مرحله ناموفق متوقف می‌شود مگر `--continue-on-error`.
- **Timeout** — هر هدف تا ۱۵ دقیقه.

---

## دستورات مرتبط

| دستور | همپوشانی |
|-------|----------|
| `theme:frontend build` | ساخت asset؛ می‌تواند npm را خودش نصب کند (`--no-install` برای رد) |
| `pinx:build` | داخل اپ `composer install --no-dev` برای پکیج قابل توزیع |
| `composer install` (دستی) | معادل `php pinoox deps install platform` |

---

## عیب‌یابی

| مشکل | چه کار کنید |
|------|-------------|
| `composer` پیدا نشد | Composer سراسری نصب کنید یا `composer.phar` در ریشه پروژه بگذارید |
| `npm` پیدا نشد | Node.js LTS نصب کنید و `npm` را در `PATH` داشته باشید |
| مرحله بدون خروجی مفید شکست خورد | با `-vv` دوباره اجرا کنید یا انتهای **Run summary** را ببینید |
| مشکل مسیر ویندوز | از ریشه پروژه `php pinoox` بزنید؛ مسیرها خودکار نرمال می‌شوند |

---

## مستندات مرتبط

- [مرجع CLI](./cli-reference.md)
- [Pinx CLI](./pinx-cli.md)
- [فرانت‌اند و Vite](../basic/frontend-vite.md)
- [وابستگی اپ‌ها](./app-depends.md) — `depends` / `use_app()` بین‌اپی (متفاوت از Composer/npm)

---

[← بازگشت به فهرست](../README.md)
