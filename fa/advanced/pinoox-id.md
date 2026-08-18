# Pinoox ID

[← بازگشت به فهرست](../README.md)

هر نصب پینوکس یک **Pinoox ID** می‌گیرد: شناسهٔ پایدار *همین اینستنس* که در اولین boot ساخته می‌شود و عوض نمی‌شود.

شناسه است، نه راز. برای شناختن نصب استفاده کنید — نه برای احراز هویت.

---

## چیست (و چه نیست)

| مقدار | نقش | عوض می‌شود؟ |
|-------|------|-------------|
| `version_name` / `version_code` | نسخهٔ نرم‌افزار | با آپدیت |
| `APP_KEY` | راز رمزنگاری | با rotate |
| دامنه / `APP_URL` | آدرس عمومی | با جابه‌جایی سایت |
| **Pinoox ID** | همین نصب | هرگز |

ID را از hostname، مسیر نصب، یا `APP_KEY` نسازید. آن‌ها تغییر می‌کنند یا راز را لو می‌دهند.

---

## فرمت و محل ذخیره

```text
px_8f3c2a91b47d4e0aa1c6d92e5f18b3c4
```

- پیشوند `px_`
- ۳۲ کاراکتر hex (UUID v4 بدون خط تیره)

در `pinker/state/identity.php` ذخیره می‌شود (gitignore). `pinker:rebuild` آن را عوض نمی‌کند.

```php
<?php

return [
    'pinoox_id' => 'px_8f3c2a91b47d4e0aa1c6d92e5f18b3c4',
    'created_at' => '2026-08-18T16:29:00+00:00',
];
```

اگر فایل نباشد، در اولین boot وب یا CLI ساخته می‌شود. اگر `pinoox_id` غیرخالی از قبل باشد، پینوکس همان را نگه می‌دارد.

---

## خواندن ID

```php
use Pinoox\Portal\Identity;

$id = pinoox_id();
$id = Identity::id();
$createdAt = Identity::createdAt();
```

helper و Portal همان فایل نصب را می‌خوانند. بعد از bootstrap (`index.php` / `pinoox`) صدا بزنید، نه در اسکریپت خام PHP.

---

## کاربردها

### ۱. هاب، مارکت، و APIهای ریموت

دامنه عوض می‌شود؛ نصب نباید عوض شود. ID را همراه بررسی لایسنس، دانلود از مارکت، یا اتصال حساب بفرستید تا بعد از تغییر دامنه همان سرور همان اینستنس بماند.

```php
use Pinoox\Portal\Http;
use Pinoox\Portal\Identity;
use Pinoox\Portal\Url;

Http::post('https://www.pinoox.com/api/manager/v1/account/getData', [
    'json' => [
        'pinoox_id' => Identity::id(),
        'remote_url' => Url::origin(),
        'token_key' => config('connect.token_key'),
    ],
]);
```

وقتی سمت ریموت باید به این نصب *اعتماد* کند، ID عمومی را با یک راز جدا (`APP_KEY`، توکن اتصال، یا instance secret) جفت کنید.

### ۲. پشتیبانی و عیب‌یابی

ID را در تیکت پشتیبانی، خروجی `doctor`، یا صفحهٔ «درباره» پنل بگذارید تا دو سایت شبیه از هم جدا شوند.

```php
return $this->ok([
    'pinoox_id' => pinoox_id(),
    'version' => config('~pinoox.version_name'),
]);
```

روی صفحات عمومی سایت چاپ نکنید.

### ۳. لایسنس و فعال‌سازی per-install

یک خرید می‌تواند به یک Pinoox ID وصل شود. کلون پروژه *بدون* `pinker/state/identity.php` ID جدید می‌سازد (نصب جدید). کپی همان فایل هویت را کپی می‌کند — برای استیج فایل را پاک کنید تا نصب جدید حساب شود.

### ۴. تله‌متری و گزارش خطا (opt-in)

اگر اپراتور موافق باشد، ID را بچسبانید تا گزارش‌های ناشناس بر اساس نصب گروه شوند، نه IP یا دامنه.

```php
Logger::error('Payment gateway timeout', [
    'pinoox_id' => pinoox_id(),
    'gateway' => 'stripe',
]);
```

داشتن ID یعنی اجازهٔ ارسال خودکار داده نیست. تله‌متری را opt-in نگه دارید.

### ۵. کلید cache، صف، و rate-limit

وقتی چند نصب یک Redis یا cache مشترک دارند، کلیدها را با ID جدا کنید تا تداخل نکنند.

```php
$cacheKey = pinoox_id() . ':market:featured';
```

### ۶. وب‌هوک و یکپارچه‌سازی خروجی

سیستم ریموت می‌تواند `pinoox_id` را به‌عنوان کلید سایت مشتری نگه دارد. URL کال‌بک عوض می‌شود؛ ID عوض نمی‌شود.

```php
use Pinoox\Portal\Http;

Http::post($partnerWebhook, [
    'json' => [
        'pinoox_id' => pinoox_id(),
        'event' => 'order.paid',
        'order_id' => $orderId,
    ],
]);
```

---

## کلون، Docker، و ساخت مجدد

| وضعیت | نتیجه |
|-------|--------|
| کلون تازه از گیت | در اولین boot، ID جدید (`pinker/` در گیت نیست) |
| کپی کل پروژه همراه `pinker/state/` | همان ID (همین اینستنس، جابه‌جا شده) |
| استیج کپی‌شده از پروداکشن | همان ID تا وقتی `pinker/state/identity.php` را پاک کنید |
| ایمیج Docker بدون volume برای `pinker/` | با هر کانتینر جدید، ID جدید |
| Docker با volume پایدار برای `pinker/` | ID بعد از restart ثابت می‌ماند |

برای ID جدید (استیج، یا اینستنس تازه روی همان فایل‌ها):

1. فایل `pinker/state/identity.php` را حذف کنید
2. یک‌بار boot کنید (وب یا CLI)

ID را داخل ایمیج Docker bake نکنید.

---

## نکات

- Pinoox ID بین ادمین‌ها و APIهای خودتان عمومی است — هرگز به‌عنوان پسورد یا JWT secret استفاده نشود
- در `platform/pinoox.config.php` ذخیره یا commit نکنید؛ آن فایل نسخهٔ توزیع است
- `.env` جای درستی نیست: کپی `.env.example` نباید ID بسازد یا تکرار کند
- دیتابیس منبع حقیقت نیست؛ دامپ هویت را کپی یا حذف می‌کند

---

## مستندات مرتبط

- [Pinker و Cache](./pinker.md)
- [پیکربندی](../basic/config.md)
- [توابع کمکی سراسری](./helpers.md)
- [Portal](../basic/portal.md)
- [Kernel و pipeline بوت](./kernel.md)

---

[← بازگشت به فهرست](../README.md)
