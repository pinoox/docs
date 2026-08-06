# مسار الملفات (File Path)

[← العودة إلى الفهرس](../README.md)

استخدم **`path()`** و Portal **`Pinoox\Portal\Path`** للوصول إلى الملفات والمجلدات على القرص. هذا يبقي الكود مستقلًا عن مكان تثبيت المشروع واسم مجلد `apps/`.

---

## الأسلوب المعياري — `path()`

```php
// Path relative to the active app
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// Config file in another app
$configFile = path('config/payment.php', 'com_acme_shop');

// App root
$appRoot = path('', 'com_acme_shop');
// or
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## استخدامات شائعة

### قراءة / كتابة ملفات

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### مسار ملف الترجمة

```php
$langFile = path('lang/en/welcome.lang.php');
```

### مسار القالب (Theme)

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

نفس سلوك `path()` مع واجهة برمجية صريحة:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // current app
Path::app('com_pinoox_manager'); // specific app
```

---

## `path()` مقابل `url()`

| المساعد | المخرجات | مثال |
|--------|--------|---------|
| `path()` | مسار فعلي على الخادم | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | عنوان HTTP للمتصفح | `https://site.com/pinoox/shop/products` |

---

## مثال: خدمة الرفع

لا تكتب الرفعات يدويًا بـ `path()` + `move_uploaded_file()` — استخدم portal **`File`** حتى تُخزَّن الملفات في مجلد `storage/` للمشروع:

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // stored under storage/local/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

راجع [إدارة الملفات](../advanced/file-management.md) لواجهة الرفع الكاملة.

---

## نصائح

- للمسارات المتاحة من المتصفح استخدم `url()` أو `assets()`، وليس `path()`.
- مرّر اسم الحزمة فقط عند الحاجة إلى تطبيق غير نشط.
- ادمج أجزاء المسار بـ `/`؛ Path يتولى الشرطة المائلة الصحيحة لنظام التشغيل.

---

## وثائق ذات صلة

- [URL والروابط](./url.md)
- [الإعدادات (Config)](./config.md)
- [خدمات التطبيق](../advanced/services.md)
- [المساعدات (Helpers)](../advanced/helpers.md)

---

[← العودة إلى الفهرس](../README.md)
