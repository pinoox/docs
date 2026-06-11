# Mocking en Pinoox

[← Volver al índice](../README.md)

Para aislar la unidad bajo prueba de dependencias externas, usa **Pest + Mockery** (`mock()`) para clases y **`fakeApp()`** para apps temporales. Ambos están disponibles en el bootstrap de tests de Pinoox.

---

## Mock de una clase — Unit

```php
// apps/com_my_shop/tests/Unit/OrderServiceTest.php

use App\com_my_shop\Component\PaymentGateway;
use App\com_my_shop\Component\OrderService;

it('charges via payment gateway', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('charge')
        ->once()
        ->with(100000)
        ->andReturn(['status' => 'paid']);

    $service = new OrderService($gateway);

    expect($service->checkout(100000))->toBe(['status' => 'paid']);
});
```

---

## Mock Portal / servicio estático

Cuando un Portal delega a un Component, prueba la lógica del Component y haz mock del Component — no del Portal directamente.

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // Inyecta el mock en el servicio bajo prueba
    });
});
```

---

## fakeApp — app temporal (tests del núcleo)

Para probar router o boot sin crear una app real:

```php
beforeEach(fn () => fakeApp('com_test_shop', [
    'app.php' => '<?php return [
        "package" => "com_test_shop",
        "enable" => true,
        "router" => ["routes" => ["routes/web.php"]],
    ];',
    'routes/web.php' => '<?php use function Pinoox\Router\get; get("/", fn() => "OK");',
]));

afterEach(fn () => deleteFakeApp('com_test_shop'));

it('loads custom routes', function () {
    expect(Pinoox\Portal\App\AppEngine::exists('com_test_shop'))->toBeTrue();
});
```

**Importante:** nunca olvides `deleteFakeApp()` en `afterEach`.

---

## HTTP falso — sin mock manual

Para endpoints reales, usa `appGet` / `appPostJson` en lugar de mockear controllers — más simple y más cercano al comportamiento de producción.

---

## Spy — verificar llamadas

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... ejecutar código
```

---

## Ejecutar tests

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Consejos

1. Haz mock solo de dependencias lentas o externas (email, pago, APIs externas).
2. Escribe lógica de integración DB y HTTP en tests Feature con base de datos de test, no mocks pesados.
3. Limpia siempre tras `fakeApp()`.

---

## Documentación relacionada

- [Primeros pasos con testing](./getting-started.md)
- [Tests HTTP](./http-tests.md)
- [Tests de consola](./console-tests.md)
- [Portal](../basic/portal.md)
- [Servicios](../advanced/services.md)

---

[← Volver al índice](../README.md)
