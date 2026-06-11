# Primeros pasos con testing en Pinoox

[← Volver al índice](../README.md)

Pinoox usa un único enfoque para el **núcleo del framework** (`tests/`) y **cada app** (`apps/{package}/tests/`): [Pest](https://pestphp.com/), un bootstrap compartido y `AppTestKit`. Esta guía recorre ese flujo estándar con ejemplos prácticos.

---

## Stack de testing

| Herramienta | Rol |
|------|------|
| Pest | Ejecutar tests PHP |
| `Pinoox\Component\Test\AppTestKit` | Arrancar entorno, app temporal, peticiones HTTP |
| `tests/bootstrap.php` | Punto de entrada compartido para tests del núcleo y de apps |

---

## Ejecutar tests

```bash
# Todos los tests del núcleo
vendor/bin/pest

# Desde CLI (selección interactiva de paquete)
php pinoox test

# Una app concreta
php pinoox test com_my_shop

# Filtrar por nombre de test
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Solo Feature o Unit
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

En CI también puedes usar los scripts de `composer.json`:

```bash
composer test          # tests de plataforma
composer test:apps     # tests de todas las apps
```

---

## Estructura de carpeta tests de la app

Ejecutar `php pinoox app:create` crea la carpeta `tests/` automáticamente:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← smoke test
    └── Unit/
```

Crear un test nuevo:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## El archivo `tests/Pest.php`

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

El helper `appPackage()` establece la app activa para helpers y autodetección.

---

## Helpers globales

| Helper | Propósito |
|--------|---------|
| `appPackage($package?)` | Establecer / leer el paquete activo |
| `inApp($package, fn)` | Ejecutar código dentro de `App::meeting()` |
| `appPath($package, $sub = '')` | Ruta a la carpeta de la app |
| `fakeApp($package, $files)` | Crear app temporal con archivos personalizados |
| `deleteFakeApp($package)` | Eliminar app temporal |
| `appGet($package, $uri, ...)` | Petición GET → `TestResponse` |
| `appPost($package, $uri, $data)` | Petición POST |
| `appPostJson($package, $uri, $json)` | Petición POST JSON |
| `pinooxBoot()` | Arrancar entorno de test |

---

## Unit — probar una clase Component

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

## Feature — smoke test de arranque de app

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## Núcleo vs app

| Ubicación | Propósito | Caso base |
|----------|---------|-----------|
| `tests/Feature/` | Framework, portales, router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, integración | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, lógica pura | `Tests\AppTestCase` |

---

## Modo test

En el entorno de test, `mode` se establece automáticamente en `test`:

```php
config('~pinoox')->get('mode'); // 'test'
```

En CI, configura `.env.testing` o `APP_ENV=test` cuando haga falta.

---

## Consejos

1. Tras `fakeApp()`, llama siempre a `deleteFakeApp()` en `afterEach`.
2. Usa `inApp()` para config, portales o modelos dentro de una app.
3. Usa `appGet` / `appPostJson` para rutas y APIs.
4. Rutas → **Feature**; clases `Component/` → **Unit**.
5. Usa `php pinoox test:create` en lugar de copiar archivos a mano.

---

## Documentación relacionada

- [Tests HTTP](./http-tests.md)
- [Tests de consola](./console-tests.md)
- [Tests de navegador (HTML)](./browser-tests.md)
- [Tests de base de datos](./database.md)
- [Mocking](./mocking.md)
- [Tu primera app](../start/your-first-app.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)
