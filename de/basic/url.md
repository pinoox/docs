# URL- und Link-Erstellung

[← Zurück zur Übersicht](../README.md)

Verwenden Sie in Pinoox 3.x **`url()`**, um interne URLs zu erstellen. Dieser Helfer nutzt **`Url::link()`** und kennt die Domain, den Installationspfad (Unterordner) und das aktuelle App-Segment.

> Verwenden Sie nicht **`Url::get()`** oder **`Url::app()`**. Nutzen Sie stattdessen **`url()`**, **`Url::link()`** und **`Url::forApp()`**.

---

## PHP — der `url()`-Helfer

```php
// Relativer Link innerhalb der aktiven App
echo url('products');              // …/shop/products
echo url('api/v1/users');          // …/shop/api/v1/users

// Accessor ohne Argumente
$accessor = url();
echo $accessor->app;               // Basis-URL der App
echo $accessor->site;              // Origin + Projektpfad
echo $accessor->api;               // API-Präfix

// Portal
use Pinoox\Portal\Url;
echo Url::link('products');        // identisch mit url('products')
echo Url::forApp('com_acme_shop'); // Basis-URL einer bestimmten App
echo Url::current();               // URL der aktuellen Seite
echo Url::origin();                // https://example.com/pinoox
```

Präfix `^` oder `~` für Links außerhalb der App-Basis:

```php
echo url('^about');                // vom Projektstamm aus
echo Url::link('^config/app.php');
```

---

## Twig — der `url()`-Accessor

```twig
{# apps/com_acme_shop/theme/default/pinoox.twig #}
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        BASE: '{{ url().appPath }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
};
```

| Accessor-Methode | Zweck |
|-----------------|---------|
| `url().site` | Origin + Projektpfad |
| `url().app` | Origin + App-Segment |
| `url().api` | API-Präfix (Standard `api/v1/`) |
| `url().resource('resources/logo.png')` | statische Datei unter `apps/{package}/` |
| `url('profile')` | Routen-Link innerhalb der App |

---

## Routenname — `route()`

```php
use function Pinoox\Router\route;

echo route('home');
echo route('product.show', ['id' => 12]);
```

---

## Theme-Assets — `assets()`

```twig
<link rel="stylesheet" href="{{ assets('dist/app.css') }}">
<script src="{{ assets('dist/main.js') }}"></script>
```

```php
echo assets('dist/main.js');    // URL der Datei im aktiven Theme
```

---

## Menü-Beispiel in einem Controller

```php
use Pinoox\Portal\View;

$menu = [
    ['label' => 'Home', 'href' => url('/')],
    ['label' => 'Products', 'href' => url('products')],
    ['label' => 'Panel', 'href' => url('panel')],
];

return View::render('layout', ['menu' => $menu]);
```

---

## Anfrage-Informationen

```php
Url::host();        // example.com
Url::scheme();      // https
Url::method();      // GET, POST, …
Url::clientIp();
Url::referer();
```

---

## Tipps

- Kodieren Sie Links nicht fest ein; verwenden Sie immer `url()` oder `Url::link()`
- Dateien in `apps/{package}/resources/` nutzen `url().resource()` oder `asset()`; Theme-Dateien nutzen **`assets()`**
- Die Basis-URL wird nicht manuell in der Konfiguration gesetzt; sie wird aus der HTTP-Anfrage erkannt

---

## Verwandte Dokumente

- [Dateipfade](./path.md)
- [Views](./views.md)
- [Twig-Templates](./templates.md)
- [Router](./routers.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zur Übersicht](../README.md)
