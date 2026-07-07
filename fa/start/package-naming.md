# قاعده نام‌گذاری پکیج (Package name)

[← بازگشت به فهرست](../README.md)

هر اپ پینوکس یک **نام پکیج** دارد — شناسه یکتا برای پوشه اپ، `app.php`، namespace، پیشوند جدول دیتابیس و دستورات CLI.

---

## فرمت

```
{scope}_{owner}_{app}
{scope}_{owner}_{app}_{module}   ← بخش چهارم اختیاری
```

| بخش | طول | توضیح | مثال |
|-----|-----|-------|------|
| **scope** | ۲ تا ۱۰ حرف | پیشوند دامنه / دسته | `com`, `ir`, `io`, `co`, `opensource` |
| **owner** | ۱+ حرف | برند، تیم یا نام کاربری | `pinoox`, `github`,   `google`, `yoosefap`, `esmaeilbahrani`, `acme` |
| **app** | ۱+ حرف | کاربرد اپ | `manager`, `shop`, `ai`, `financial` |
| **module** | اختیاری | زیرماژول داخل همان اپ | `panel`, `api`, `admin` |

### مثال‌های معتبر

```
com_pinoox_manager
com_acme_shop
co_pinoox_app
ir_mysite_financial
io_pinoox_ai
com_acme_shop_panel
opensource_acme_blog
```

### مثال‌های نامعتبر

```
manager              ← alias کوتاه، نه نام پکیج
com_shop             ← فقط دو بخش
bad-name             ← خط تیره مجاز نیست
a_b_c                ← scope باید حداقل ۲ حرف باشد
```

---

## قوانین (اعتبارسنجی فریم‌ورک)

- کاراکترها: حروف **کوچک** `a-z`، عدد `0-9`، underscore `_`
- ساختار: **۳ یا ۴** بخش با `_`
- scope: **۲ تا ۱۰** حرف، شروع با حرف
- حداکثر طول کل: **۶۴** کاراکتر
- **ورودی case-insensitive**: `COM_PINOOX_MANAGER` و `com_pinoox_manager` یک پکیج هستند (ذخیره به صورت lowercase)

---

## یک نام، سه جا

نام پکیج باید همه‌جا یکسان باشد:

```
apps/io_pinoox_ai/          ← نام پوشه
app.php → 'package' => 'io_pinoox_ai'
App\io_pinoox_ai\Controller\...   ← namespace در PHP
```

---

## scopeهای پیشنهادی

| scope | کاربرد |
|-------|--------|
| `com` | پیش‌فرض اپ‌های عمومی (wizard برای نام کوتاه `com_` اضافه می‌کند) |
| `ir` | پروژه‌ها یا برندهای ایرانی |
| `io` | ابزار شخصی و سرویس‌های کوچک |
| `co` | اپ‌های سازمانی / شرکتی |
| `dev` | توسعه و آزمایش |

اپ‌های سیستمی پینوکس: `com_pinoox_*` (مثل `com_pinoox_manager`).

---

## رفتار wizard (`app:create`)

| ورودی شما | خروجی |
|-----------|--------|
| `my_shop` | `com_my_shop` |
| `com_acme_blog` | `com_acme_blog` |
| `io_pinoox_ai` | `io_pinoox_ai` (بدون تغییر) |
| `IO_PINOOX_AI` | `io_pinoox_ai` (نرمال به lowercase) |

---

## دیتابیس و route

- پیشوند جدول معمولاً همان نام پکیج است: `io_pinoox_ai_users`
- پیشوند route از **app slug** (بخش بعد از `{scope}_{owner}_`) گرفته می‌شود: `com_pinoox_manager` → `manager.`

---

## مرجع API

اعتبارسنجی در `Pinoox\Component\Package\PackageName`:

| متد | کاربرد |
|-----|--------|
| `isValid($name)` | بررسی فرمت |
| `normalize($name)` / `canonical($name)` | فرم استاندارد lowercase |
| `equals($a, $b)` | مقایسه بدون حساسیت به حروف |
| `looksLike($value)` | تشخیص پکیج در CLI |
| `appSlug($package)` | استخراج slug برای route |

بیشتر بخوانید: [مرجع app.php](./app-manifest.md) و [ساختار پروژه](./structure.md).
