# Erste Schritte beim Testen in Pinoox

[← Zurück zum Index](../README.md)

Pinoox verwendet einen einheitlichen Ansatz für den **Framework-Core** (`tests/`) und **jede App** (`apps/{package}/tests/`): [Pest](https://pestphp.com/), ein gemeinsames Bootstrap und `AppTestKit`. Diese Anleitung führt den Standard-Workflow mit praktischen Beispielen durch.

---

## Test-Stack

| Werkzeug | Rolle |
|------|------|
| Pest | PHP-Tests ausführen |
| `Pinoox\Component\Test\AppTestKit` | Umgebung booten, temporäre App, HTTP-Anfragen |
| `tests/bootstrap.php` | Gemeinsamer Einstiegspunkt für Core- und App-Tests |

---

## Tests ausführen

```bash
# Alle Core-Tests
vendor/bin/pest

# Über CLI (interaktive Package-Auswahl)
php pinoox test

# Eine bestimmte App
php pinoox test com_my_shop

# Nach Testname filtern
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Nur Feature oder Unit
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

In CI können auch die Skripte in `composer.json` verwendet werden:

```bash
composer test          # Plattform-Tests
composer test:apps     # alle App-Tests
```

---

## App-Test-Ordnerstruktur

`php pinoox app:create` erstellt den `tests/`-Ordner automatisch:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← Bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← Smoke-Test
    └── Unit/
```

Neuen Test erstellen:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## Die Datei `tests/Pest.php`

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

Der `appPackage()`-Helper setzt die aktive App für Helper und Auto-Erkennung.

---

## Globale Helper

| Helper | Zweck |
|--------|---------|
| `appPackage($package?)` | Aktives Package setzen / lesen |
| `inApp($package, fn)` | Code innerhalb von `App::meeting()` ausführen |
| `appPath($package, $sub = '')` | Pfad zum App-Ordner |
| `fakeApp($package, $files)` | Temporäre App mit eigenen Dateien erstellen |
| `deleteFakeApp($package)` | Temporäre App entfernen |
| `appGet($package, $uri, ...)` | GET-Anfrage → `TestResponse` |
| `appPost($package, $uri, $data)` | POST-Anfrage |
| `appPostJson($package, $uri, $json)` | JSON-POST-Anfrage |
| `pinooxBoot()` | Testumgebung booten |

---

## Unit — Component-Klasse testen

```php
// apps/com_my_shop/tests/Unit/PriceTest.php

it('calculates discount', function () {
    $package = appPackage();

    inApp($package, function () {
        $price = new App\com_my_shop\Component\PriceHelper();
        expect($price->discount(100, 10))->toBe(90);
    });
});
```

---

## Feature — App-Boot-Smoke-Test

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## Core vs. App

| Speicherort | Zweck | Basisklasse |
|----------|---------|-----------|
| `tests/Feature/` | Framework, Portals, Router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, Integration | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, reine Logik | `Tests\AppTestCase` |

---

## Testmodus

In der Testumgebung wird `mode` automatisch auf `test` gesetzt:

```php
config('~pinoox')->get('mode'); // 'test'
```

In CI bei Bedarf `.env.testing` oder `APP_ENV=test` konfigurieren.

---

## Tipps

1. Nach `fakeApp()` immer `deleteFakeApp()` in `afterEach` aufrufen.
2. `inApp()` für Config, Portals oder Models innerhalb einer App verwenden.
3. `appGet` / `appPostJson` für Routen und APIs verwenden.
4. Routen → **Feature**; `Component/`-Klassen → **Unit**.
5. `php pinoox test:create` statt Dateien von Hand kopieren.

---

## Verwandte Dokumentation

- [HTTP-Tests](./http-tests.md)
- [Konsolen-Tests](./console-tests.md)
- [Browser- (HTML-)Tests](./browser-tests.md)
- [Datenbank-Tests](./database.md)
- [Mocking](./mocking.md)
- [Ihre erste App](../start/your-first-app.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zum Index](../README.md)
