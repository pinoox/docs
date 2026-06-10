# مسیر فایل (Path)

[← بازگشت به فهرست](../../readme-fa.md)

برای دسترسی به فایل و پوشه در دیسک از **`path()`** و Portal **`Pinoox\Portal\Path`** استفاده کنید. این روش کد را مستقل از محل نصب پروژه و نام پوشه `apps/` نگه می‌دارد.

---

## روش استاندارد — `path()`

```php
// مسیر نسبی به اپ فعال
$uploadDir = path('uploads/avatars');
// → …/apps/com_acme_shop/uploads/avatars

// مسیر فایل config اپ دیگر
$configFile = path('config/payment.php', 'com_acme_shop');

// مسیر ریشه اپ
$appRoot = path('', 'com_acme_shop');
```

---

## Portal — `Path::get()`

همان رفتار `path()` با API صریح:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::get('lang/fa/welcome.lang.php', 'com_acme_shop');
Path::app();                         // مسیر اپ جاری
Path::app('com_pinoox_manager');     // مسیر اپ مشخص
Path::apps();                        // پوشه apps/
Path::root();                        // ریشه پروژه
```

---

## کاربردهای رایج

### خواندن/نوشتن فایل

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### مسیر ترجمه

```php
$langFile = path('lang/fa/welcome.lang.php');
```

### مسیر theme

```php
$themeDir = path('theme/default');
```

---

## تفاوت `path()` و `url()`

| تابع | خروجی | مثال |
|------|--------|------|
| `path()` | مسیر فیزیکی روی سرور | `/var/www/pinoox/apps/com_acme_shop/uploads` |
| `url()` | آدرس HTTP برای مرورگر | `https://site.com/pinoox/shop/products` |

---

## مثال: سرویس آپلود

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

class UploadService
{
    public function store(array $file, string $subdir = 'products'): string
    {
        $dest = path('uploads/' . $subdir);
        if (!is_dir($dest)) {
            mkdir($dest, 0755, true);
        }
        $name = uniqid() . '_' . $file['name'];
        move_uploaded_file($file['tmp_name'], $dest . '/' . $name);
        return $name;
    }
}
```

---

## نکات

- برای مسیرهای public در مرورگر از `url()` یا `assets()` استفاده کنید، نه `path()`
- نام package را فقط وقتی بدهید که به اپ غیرفعال نیاز دارید
- مسیرها را با `/` بچسبانید؛ Path slash مناسب OS را مدیریت می‌کند

---

## مستندات مرتبط

- [URL و لینک](url.md)
- [پیکربندی](config.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../../readme-fa.md)
