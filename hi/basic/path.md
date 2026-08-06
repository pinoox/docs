# File Path

[← इंडेक्स पर वापस जाएँ](../README.md)

Disk पर files और folders तक पहुँचने के लिए **`path()`** और **`Pinoox\Portal\Path`** Portal का उपयोग करें। इससे code project install location और `apps/` folder के नाम से independent रहता है।

---

## Standard approach — `path()`

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

## सामान्य उपयोग

### Read / write files

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Translation file path

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Theme path

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

`path()` जैसा व्यवहार explicit API के साथ:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // current app
Path::app('com_pinoox_manager'); // specific app
```

---

## `path()` vs `url()`

| Helper | Output | Example |
|--------|--------|---------|
| `path()` | Physical path on the server | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | HTTP URL for the browser | `https://site.com/pinoox/shop/products` |

---

## Example: upload service

`path()` + `move_uploaded_file()` से uploads manually न लिखें — **`File`** portal का उपयोग करें ताकि files project `storage/` folder में जाएँ:

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

पूरे upload API के लिए [File management](../advanced/file-management.md) देखें।

---

## Tips

- Browser-accessible paths के लिए `url()` या `assets()` उपयोग करें, `path()` नहीं।
- Package name तभी pass करें जब non-active app चाहिए।
- Path segments `/` से join करें; Path सही OS slash handle करता है।

---

## संबंधित docs

- [URL and Links](./url.md)
- [Config](./config.md)
- [App Services](../advanced/services.md)
- [Helpers](../advanced/helpers.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
