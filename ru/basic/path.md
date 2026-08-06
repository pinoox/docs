# Пути к файлам

[← Вернуться к оглавлению](../README.md)

Используйте **`path()`** и Portal **`Pinoox\Portal\Path`** для доступа к файлам и папкам на диске. Это делает код независимым от того, где установлен проект и как называется папка `apps/`.

---

## Стандартный подход — `path()`

```php
// Путь относительно активного приложения
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// Файл конфигурации в другом приложении
$configFile = path('config/payment.php', 'com_acme_shop');

// Корень приложения
$appRoot = path('', 'com_acme_shop');
// или
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## Частые случаи

### Чтение / запись файлов

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Путь к файлу перевода

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Путь к теме

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

То же поведение, что у `path()`, с явным API:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // текущее приложение
Path::app('com_pinoox_manager'); // конкретное приложение
```

---

## `path()` vs `url()`

| Хелпер | Результат | Пример |
|--------|--------|---------|
| `path()` | Физический путь на сервере | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | HTTP URL для браузера | `https://site.com/pinoox/shop/products` |

---

## Пример: сервис загрузки

Не записывайте загрузки вручную через `path()` + `move_uploaded_file()` — используйте Portal **`File`**, чтобы файлы попадали в папку `storage/` проекта:

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // сохраняется в storage/local/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

Полный API загрузки см. в [Управление файлами](../advanced/file-management.md).

---

## Советы

- Для путей, доступных из браузера, используйте `url()` или `assets()`, а не `path()`.
- Передавайте имя пакета только когда нужно неактивное приложение.
- Соединяйте сегменты пути через `/`; Path сам обработает правильный слэш ОС.

---

## Связанные документы

- [URL и ссылки](./url.md)
- [Конфигурация](./config.md)
- [Сервисы приложения](../advanced/services.md)
- [Хелперы](../advanced/helpers.md)

---

[← Вернуться к оглавлению](../README.md)
