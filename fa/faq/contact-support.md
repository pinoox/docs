# تماس با پشتیبانی

[← بازگشت به فهرست](../README.md)

اگر بعد از [مشکلات رایج](./common-issues.md) هنوز blocker دارید، از کانال‌های رسمی زیر استفاده کنید. قبل از تماس، نسخه پینوکس، PHP، پیام خطا و مراحل reproduce را آماده کنید.

---

## پشتیبانی عمومی

**ایمیل:** [support@pinoox.com](mailto:support@pinoox.com)

مناسب برای:

- سوالات نصب و deploy
- رفتار غیرمنتظره فریمورک
- راهنمایی معماری HMVC و اپ

در ایمیل این موارد را بفرستید:

1. نسخه پینوکس (`composer.json` → `version` یا tag گیت)
2. نسخه PHP (`php -v`)
3. سیستم‌عامل و وب‌سرور (Apache/nginx، MAMP، cPanel، …)
4. متن کامل خطا یا screenshot
5. حداقل مراحل reproduce

---

## GitHub Issues

برای باگ قطعی، feature request و بحث فنی عمومی:

**مخزن:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

قبل از issue جدید:

- issue تکراری جستجو کنید
- روی آخرین نسخه stable/beta تست کنید
- اگر مربوط به `pincore` است، package `pinoox/pincore` را هم بررسی کنید

قالب پیشنهادی issue:

```markdown
## محیط
- Pinoox: 3.1-beta
- PHP: 8.2.x
- OS: Windows / Linux

## انتظار
...

## واقعیت
...

## مراحل reproduce
1. ...
2. ...
```

---

## گزارش امنیتی

**ایمیل:** [security@pinoox.com](mailto:security@pinoox.com)

**فقط** برای آسیب‌پذیری‌های امنیتی — SQL injection، bypass احراز هویت، RCE، افشای secret.

- جزئیات را public (GitHub issue) منتشر نکنید تا patch آماده شود
- در صورت امکان PoC minimal و impact را توضیح دهید

---

## مشارکت در کد

برای PR و توسعه فریمورک:

- [مشارکت در توسعه](../introduction/contributions.md)
- Fork → branch → تست (`php pinoox test`) → Pull Request

---

## منابع خودیاری

| موضوع | سند |
|-------|-----|
| نصب | [installing-pinoox.md](../start/installing-pinoox.md) |
| اولین اپ | [your-first-app.md](../start/your-first-app.md) |
| مشکلات رایج | [common-issues.md](./common-issues.md) |
| تست | [getting-started.md](../test/getting-started.md) |

**وب‌سایت:** [pinoox.com](https://www.pinoox.com/)

---

## مستندات مرتبط

- [مشکلات رایج](./common-issues.md)
- [پینوکس چیست؟](../introduction/what-is-pinoox.md)
- [مشارکت در توسعه](../introduction/contributions.md)
- [نصب و راه‌اندازی](../start/installing-pinoox.md)

---

[← بازگشت به فهرست](../README.md)
