# Mocking in Pinoox

[← Zurück zum Index](../README.md)

Zum Isolieren der zu testenden Einheit von externen Abhängigkeiten **Pest + Mockery** (`mock()`) für Klassen und **`fakeApp()`** für temporäre Apps verwenden. Beides ist im Pinoox-Test-Bootstrap verfügbar.

---

## Klasse mocken — Unit

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

## Portal / statischen Service mocken

Wenn ein Portal an eine Component delegiert, die Component-Logik testen und die Component mocken — nicht das Portal direkt.

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // Mock in den zu testenden Service injizieren
    });
});
```

---

## fakeApp — temporäre App (Core-Tests)

Zum Testen von Router oder Boot ohne echte App:

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

**Wichtig:** `deleteFakeApp()` in `afterEach` nie vergessen.

---

## Fake HTTP — kein manuelles Mock

Für echte Endpunkte `appGet` / `appPostJson` statt Controller zu mocken — einfacher und näher am Produktionsverhalten.

---

## Spy — Aufrufe verifizieren

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... Code ausführen
```

---

## Tests ausführen

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Tipps

1. Nur langsame oder externe Abhängigkeiten mocken (E-Mail, Zahlung, externe APIs).
2. DB- und HTTP-Integrationslogik in Feature-Tests mit Testdatenbank schreiben, nicht mit schweren Mocks.
3. Nach `fakeApp()` immer aufräumen.

---

## Verwandte Dokumentation

- [Erste Schritte beim Testen](./getting-started.md)
- [HTTP-Tests](./http-tests.md)
- [Konsolen-Tests](./console-tests.md)
- [Portal](../basic/portal.md)
- [Services](../advanced/services.md)

---

[← Zurück zum Index](../README.md)
