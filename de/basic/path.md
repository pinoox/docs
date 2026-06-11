# Dateipfade

[← Zurück zur Übersicht](../README.md)

Verwenden Sie **`path()`** und das Portal **`Pinoox\Portal\Path`**, um auf Dateien und Ordner auf der Festplatte zuzugreifen. So bleibt der Code unabhängig davon, wo das Projekt installiert ist und wie der `apps/`-Ordner heißt.

---

## Standardansatz — `path()`

```php
// Pfad relativ zur aktiven App
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// Konfigurationsdatei in einer anderen App
$configFile = path('config/payment.php', 'com_acme_shop');

// App-Stammverzeichnis
$appRoot = path('', 'com_acme_shop');
// oder
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## Häufige Anwendungsfälle

### Dateien lesen / schreiben

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Pfad zu einer Übersetzungsdatei

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Theme-Pfad

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

Gleiches Verhalten wie `path()` mit einer expliziten API:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // aktuelle App
Path::app('com_pinoox_manager'); // bestimmte App
```

---

## `path()` vs. `url()`

| Helfer | Ausgabe | Beispiel |
|--------|--------|---------|
| `path()` | Physischer Pfad auf dem Server | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | HTTP-URL für den Browser | `https://site.com/pinoox/shop/products` |

---

## Beispiel: Upload-Service

Schreiben Sie Uploads nicht manuell mit `path()` + `move_uploaded_file()` — verwenden Sie das **`File`**-Portal, damit Dateien im `storage/`-Ordner des Projekts landen:

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // gespeichert unter storage/apps/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

Siehe [Dateiverwaltung](../advanced/file-management.md) für die vollständige Upload-API.

---

## Tipps

- Für browserzugängliche Pfade verwenden Sie `url()` oder `assets()`, nicht `path()`.
- Geben Sie einen Paketnamen nur an, wenn Sie eine nicht aktive App benötigen.
- Verbinden Sie Pfadsegmente mit `/`; Path kümmert sich um den korrekten OS-Schrägstrich.

---

## Verwandte Dokumente

- [URL und Links](./url.md)
- [Konfiguration](./config.md)
- [App-Services](../advanced/services.md)
- [Helfer](../advanced/helpers.md)

---

[← Zurück zur Übersicht](../README.md)
