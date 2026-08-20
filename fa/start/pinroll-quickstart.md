# Pinroll — راهنمای سریع برای توسعه‌دهنده

[← بازگشت به فهرست](../README.md)

این صفحه برای کسی است که می‌خواهد **بدون ورود به جزئیات فنی**، اپ یا پلتفرم پینوکس را روی هاست بفرستد.

برای مرجع کامل (همه فلگ‌ها، PinGate، provision و …): [Pinroll — انتشار و دیپلوی](../deploy/pinroll.md)

---

## Pinroll چیست؟

Pinroll ابزار دیپلوی پینوکس است. روی **ماشین خودت** نصب می‌شود و با FTP (یا SSH) فایل‌ها را به هاست می‌فرستد و از راه **PinGate** (`pingate.php` روی سرور) نصب می‌کند.

هاست به Pinroll داخل `vendor/` نیاز **ندارد** — فقط یک فایل `pingate.php` کافی است.

---

## نصب (یک‌بار)

```bash
composer require --dev pinoox/pinroll
php pinoox pinroll:init
```

بعد اطلاعات هاست را پر کنید:

| چه چیزی | کجا |
|---------|-----|
| آدرس FTP، کاربر، رمز | `.env` با پیشوند `PINROLL_` **یا** `.pinoox/pinroll.config.php` |
| آدرس سایت + توکن PinGate | `.pinoox/pinroll.config.php` (بعد از `connect`) |

فایل `.pinoox/` در Git نیست — رمز و توکن را آنجا نگه دارید.

---

## سه حالت رایج

### الف) هاست خالی — اولین بار سایت را می‌سازید

پوشه FTP خالی است و هنوز `index.php` ندارید.

```bash
# در .env: PINROLL_HOST, PINROLL_USER, PINROLL_PASSWORD, PINROLL_SITE
# و اطلاعات دیتابیس: PINROLL_DB_*
php pinoox pinroll:provision
```

بعد از موفقیت، سایت بالا می‌آید. به‌روزرسانی‌های بعدی با `deploy` است، نه دوباره `provision`.

---

### ب) سایت از قبل بالا است — فقط وصل شوید

```bash
php pinoox pinroll:connect
php pinoox pinroll:check
php pinoox pinroll:deploy
```

`connect` یک‌بار می‌پرسد: مسیر FTP، آدرس سایت، و `pingate.php` را آپلود می‌کند. توکن در `.pinoox/pinroll.config.php` ذخیره می‌شود.

---

### ج) فقط یک اپ را آپدیت کنید

```bash
php pinoox pinroll:deploy --app=com_pinoox_manager
```

یا اگر اپ پیش‌فرض را در کانفیگ گذاشته‌اید:

```bash
php pinoox pinroll:deploy
```

---

## deploy چه کارهایی انجام می‌دهد؟

وقتی `pinroll:deploy` می‌زنید (با نصب روی سرور)، معمولاً این مراحل را می‌بینید:

1. **Build** — ساخت پکیج `.pinx`
2. **Connect FTP** — اتصال به هاست
3. **Ensure PinGate** — بررسی سلامت `pingate.php`
4. **Cleanup leftovers** — پاک‌سازی فایل‌های قدیمی/ناقص (آرشیو، tmp، zip باقی‌مانده)
5. **Upload** — فرستادن `.pinx` به هاست
6. **Install** — نصب از طریق PinGate

فقط آپلود بدون نصب:

```bash
php pinoox pinroll:push --app=com_pinoox_manager
php pinoox pinroll:install --app=com_pinoox_manager
```

---

## تنظیمات — ساده

**آدرس سایت** را فقط origin بنویسید، نه آدرس کامل PinGate:

```text
✅ https://example.com
❌ https://example.com/pingate.php?route=
```

Pinroll خودش `/pingate.php?route=` را اضافه می‌کند.

**توکن** مثل رمز FTP است — **یک توکن برای هر هاست**، بین هم‌تیمی‌ها مشترک:

- نفر اول: `pinroll:connect` → توکن ساخته و در overlay ذخیره می‌شود
- بقیه: همان توکن را در `.pinoox/pinroll.config.php` یا `.env` (`PINROLL_TOKEN`) کپی کنند

برای دیدن کانفیگ نهایی (بدون نمایش توکن):

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
| آپدیت فقط pincore | `php pinoox pinroll:pincore` |
| sync پوشه دلخواه | `php pinoox pinroll:sync --from=./path --to=remote/path` |
| آپدیت دستی pingate | `php pinoox pinroll:gate` |

---

## اگر خطا داد

| پیام / علامت | کار ساده |
|--------------|----------|
| `401` / Missing bearer token | توکن در کانفیگ با هاست یکی نیست؛ از هم‌تیمی بپرسید یا دوباره `connect` |
| `503` یا PinGate جواب نمی‌دهد | `php pinoox pinroll:gate` بزنید؛ deploy جدید خودش هم pingate را چک می‌کند |
| خطای FTP | `pinroll:check` و مقادیر `PINROLL_HOST` / `USER` / `PASSWORD` |
| نصب شکست خورد | لاگ را ببینید: `storage/pinroll/gate/` (روی ماشین توسعه) |
| ویندوز / MAMP و خطای HTTPS | Pinroll 1.5.2+ معمولاً خودش حل می‌کند؛ `pinroll:check` را دوباره بزنید |

---

## نکته امنیتی

بدون توکن، PinGate فقط `401` برمی‌گرداند — کسی نمی‌تواند از بیرون نصب یا rollback بزند.

توکن را در Git commit نکنید. `.pinoox/` و `.env` را gitignore نگه دارید.

---

## بعد از deploy

اگر migration یا patch دارید:

```bash
php pinoox pinroll:setup
```

یا روی خود هاست (SSH):

```bash
php pinoox migrate com_pinoox_manager
php pinoox cache:build com_pinoox_manager
```

---

## مستندات بیشتر

- [Pinroll — انتشار و دیپلوی (کامل)](../deploy/pinroll.md)
- [مروری بر Pinroll](../advanced/pinroll.md)
- [مشکلات رایج — Pinroll](../faq/common-issues.md#pinroll)

---

[← بازگشت به فهرست](../README.md)
