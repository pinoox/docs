# File Path

[← Back to index](../README.md)

Use **`path()`** and the **`Pinoox\Portal\Path`** Portal to access files and folders on disk. This keeps code independent of where the project is installed and what the `apps/` folder is named.

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
| `path()` | Physical path on the server | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | HTTP URL for the browser | `https://site.com/pinoox/shop/products` |

---

## Example: upload service

Do not write uploads manually with `path()` + `move_uploaded_file()` — use the **`File`** portal so files land in the project `storage/` folder:

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

See [File management](../advanced/file-management.md) for the full upload API.

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

[← Back to index](../README.md)
