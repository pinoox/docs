# Rutas de archivos (Path)

[← Volver al índice](../README.md)

Usa **`path()`** y el Portal **`Pinoox\Portal\Path`** para acceder a archivos y carpetas en disco. Esto mantiene el código independiente de dónde está instalado el proyecto y de cómo se llama la carpeta `apps/`.

---

## Enfoque estándar — `path()`

```php
// Ruta relativa a la app activa
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// Archivo de configuración de otra app
$configFile = path('config/payment.php', 'com_acme_shop');

// Raíz de la app
$appRoot = path('', 'com_acme_shop');
// o
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## Usos comunes

### Leer / escribir archivos

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Ruta de un archivo de traducción

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Ruta del tema

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

Mismo comportamiento que `path()` con una API explícita:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // app actual
Path::app('com_pinoox_manager'); // app específica
```

---

## `path()` vs `url()`

| Helper | Salida | Ejemplo |
|--------|--------|---------|
| `path()` | Ruta física en el servidor | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | URL HTTP para el navegador | `https://site.com/pinoox/shop/products` |

---

## Ejemplo: servicio de subida de archivos

No escribas las subidas manualmente con `path()` + `move_uploaded_file()` — usa el portal **`File`** para que los archivos terminen en la carpeta `storage/` del proyecto:

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // se almacena en storage/apps/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

Consulta [Gestión de archivos](../advanced/file-management.md) para la API completa de subida.

---

## Consejos

- Para rutas accesibles desde el navegador usa `url()` o `assets()`, no `path()`.
- Pasa un nombre de paquete solo cuando necesites una app que no sea la activa.
- Une los segmentos de ruta con `/`; Path se encarga de la barra correcta del sistema operativo.

---

## Documentación relacionada

- [URL y enlaces](./url.md)
- [Configuración (Config)](./config.md)
- [Servicios de la app](../advanced/services.md)
- [Helpers](../advanced/helpers.md)

---

[← Volver al índice](../README.md)
