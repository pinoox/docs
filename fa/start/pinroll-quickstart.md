# Pinroll — راهنمای سریع برای توسعه‌دهنده

[← بازگشت به فهرست](../README.md)

این صفحه برای کسی است که می‌خواهد **بدون ورود به جزئیات فنی**، اپ یا پلتفرم پینوکس را روی هاست بفرستد.

برای مرجع کامل (همه فلگ‌ها، PinGate، provision و …): [Pinroll — انتشار و دیپلوی](../deploy/pinroll.md)

---

## Pinroll چیست؟

Pinroll ابزار دیپلوی پینوکس است. روی **ماشین خودت** نصب می‌شود و فایل‌ها را با **FTP**، **SSH** یا **Pinion** (آپلود HTTP تکه‌ای) به هاست می‌فرستد و از راه **PinGate** (`pingate.php` روی سرور) نصب می‌کند.

هاست به Pinroll داخل `vendor/` نیاز **ندارد** — فقط یک فایل `pingate.php` کافی است.

---

## نصب (یک‌بار)

```bash
composer require --dev pinoox/pinroll
php pinoox pinroll:init
```

---

## روش راه‌اندازی را انتخاب کنید

| روش | کی | دستور |
|-----|-----|--------|
| **Zip kit** | بدون FTP — فقط File Manager | `php pinoox pinroll:kit` |
| **FTP** | هاست اشتراکی | `php pinoox pinroll:connect --via=ftp` |
| **SSH** | VPS | `php pinoox pinroll:connect --via=ssh` |
| **FTP یک‌بار → Pinion** | اول gate با FTP، بعد آپلود HTTP | `php pinoox pinroll:connect --bootstrap-ftp` |
| **انتخاب تعاملی** | نمی‌دانی کدام | `php pinoox pinroll:connect` |

### Zip kit (بدون FTP) — ساده‌ترین برای خیلی از هاست‌ها

```bash
php pinoox pinroll:kit
# → storage/pinroll/pinroll-kit-production.zip
```

zip را داخل `public_html` استخراج کنید (باید ببینید: `pingate.php` و `storage/pinroll/tokens/…`). بعد:

```bash
php pinoox pinroll:check
php pinoox pinroll:deploy
```

از این به بعد آپلودها با **Pinion** (HTTP) انجام می‌شود — دیگر به FTP نیاز نیست.

---

## سه حالت رایج

### الف) هاست خالی — اولین بار سایت را می‌سازید

پوشه خالی است و هنوز `index.php` ندارید. معمولاً با FTP/SSH:

```bash
# در .env: PINROLL_HOST, PINROLL_USER, PINROLL_PASSWORD, PINROLL_SITE
# و اطلاعات دیتابیس: PINROLL_DB_*
php pinoox pinroll:provision
```

بعد از موفقیت، سایت بالا می‌آید. به‌روزرسانی‌های بعدی با `deploy` است، نه دوباره `provision`.

---

### ب) سایت از قبل بالا است — فقط وصل شوید

```bash
php pinoox pinroll:connect          # منوی روش‌ها، یا --via=pinion / ftp / ssh
php pinoox pinroll:check
php pinoox pinroll:deploy
```

`connect` مسیر دیپلوی + آدرس سایت را می‌پرسد و PinGate را آماده می‌کند (آپلود خودکار یا kit zip). توکن در `.pinoox/pinroll.config.php` ذخیره می‌شود.

---

### ج) فقط یک اپ را آپدیت کنید

```bash
php pinoox pinroll:deploy --app=com_pinoox_manager
```

---

## deploy چه کارهایی انجام می‌دهد؟

1. **Build** — ساخت پکیج `.pinx`
2. **Connect** — اتصال با ترنسپورت هاست (FTP / SSH / Pinion)
3. **Ensure PinGate** — سلامت `pingate.php`
4. **Cleanup leftovers** — پاک‌سازی فایل‌های قدیمی/ناقص
5. **Upload** — فرستادن `.pinx`
6. **Install** — نصب از طریق PinGate

فقط آپلود بدون نصب:

```bash
php pinoox pinroll:push --app=com_pinoox_manager
php pinoox pinroll:install --app=com_pinoox_manager
```

---

## تنظیمات — ساده

**آدرس سایت** را فقط origin بنویسید:

```text
✅ https://example.com
❌ https://example.com/pingate.php?route=
```

**توکن** مثل رمز هاست است — **یک توکن برای هر هاست**، بین هم‌تیمی‌ها مشترک.

```bash
php pinoox pinroll:config
```

---

## دستورهای روزمره

| کار | دستور |
|-----|--------|
| دیپلوی اپ | `php pinoox pinroll:deploy --app=نام_پکیج` |
| دیپلوی پلتفرم + همه اپ‌ها | `php pinoox pinroll:deploy --full` |
| migrate بعد از دیپلوی | `php pinoox pinroll:setup` |
| برگشت به نسخه قبل | `php pinoox pinroll:rollback` |
| بررسی اتصال | `php pinoox pinroll:check` |
| بستهٔ extract بدون FTP | `php pinoox pinroll:kit` |
| آپدیت فقط pincore | `php pinoox pinroll:pincore` |
| sync پوشه دلخواه | `php pinoox pinroll:sync --from=./path --to=remote/path` |
| آپدیت دستی pingate | `php pinoox pinroll:gate` |

`pincore` و `sync` پوشه را **zip** می‌کنند، با همان `via` هاست آپلود می‌کنند و روی سرور با PinGate (`POST ?route=sync`) استخراج می‌کنند — فقط FTP نیست.

---

## اگر خطا داد

| پیام / علامت | کار ساده |
|--------------|----------|
| `401` / Missing bearer token | توکن در کانفیگ با هاست یکی نیست؛ از هم‌تیمی بپرسید یا دوباره `connect` / `kit` |
| `503` یا PinGate جواب نمی‌دهد | `php pinoox pinroll:gate`؛ deploy جدید خودش هم pingate را چک می‌کند |
| خطای FTP | `pinroll:check` و مقادیر `PINROLL_HOST` / `USER` / `PASSWORD` |
| `Action "…" is already registered` | pingate را به‌روز کنید (`pinroll:gate`)؛ نصب با skip_cache + rebuild کش |
| نصب شکست خورد | لاگ: `storage/pinroll/gate/` روی ماشین توسعه |
| ویندوز / MAMP و خطای HTTPS | Pinroll 1.5.2+ معمولاً خودش حل می‌کند؛ `pinroll:check` |

---

## نکته امنیتی

بدون توکن، PinGate فقط `401` برمی‌گرداند. توکن را در Git commit نکنید.

---

## بعد از deploy

```bash
php pinoox pinroll:setup
```

---

## مستندات بیشتر

- [Pinroll — انتشار و دیپلوی (کامل)](../deploy/pinroll.md)
- [مروری بر Pinroll](../advanced/pinroll.md)
- [مشکلات رایج — Pinroll](../faq/common-issues.md#pinroll)

---

[← بازگشت به فهرست](../README.md)
