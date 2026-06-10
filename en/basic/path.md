# File Path

[← Back to index](../../README.md)

Use **`path()`** and the **`Pinoox\Portal\Path`** Portal to access files and folders on disk. This keeps code independent of where the project is installed and what the `apps/` folder is named.

---

## Standard approach — `path()`

```php
// Path relative to the active app
$uploadDir = path('uploads/avatars');
// → …/apps/com_acme_shop/uploads/avatars

// Config file in another app
$configFile = path('config/payment.php', 'com_acme_shop');

// App root
$appRoot = path('', 'com_acme_shop');
// or
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## Common uses

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

Same behavior as `path()` with an explicit API:

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
| `path()` | Physical path on the server | `/var/www/pinoox/apps/com_acme_shop/uploads` |
| `url()` | HTTP URL for the browser | `https://site.com/pinoox/shop/uploads` |

---

## Example: upload service

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

## Tips

- For browser-accessible paths use `url()` or `assets()`, not `path()`.
- Pass a package name only when you need a non-active app.
- Join path segments with `/`; Path handles the correct OS slash.

---

## Related docs

- [URL and Links](./url.md)
- [Config](./config.md)
- [App Services](../advanced/services.md)
- [Helpers](../advanced/helpers.md)

---

[← Back to index](../../README.md)
