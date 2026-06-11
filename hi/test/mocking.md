# Mocking in Pinoox

[← इंडेक्स पर वापस जाएँ](../README.md)

Unit under test को external dependencies से isolate करने के लिए classes के लिए **Pest + Mockery** (`mock()`) और temporary apps के लिए **`fakeApp()`** उपयोग करें। दोनों Pinoox test bootstrap में available हैं।

---

## Mock a class — Unit

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

## Mock Portal / static service

जब Portal Component को delegate करे, Component logic test करें और Component mock करें — Portal directly नहीं।

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // Inject the mock into the service under test
    });
});
```

---

## fakeApp — temporary app (core tests)

Real app बनाए बिना router या boot test करने के लिए:

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

**Important:** `afterEach` में `deleteFakeApp()` कभी न भूलें।

---

## Fake HTTP — no manual mock

Real endpoints के लिए controllers mock करने की जगह `appGet` / `appPostJson` उपयोग करें — simpler और production behavior के करीब।

---

## Spy — verify calls

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... run code
```

---

## Running tests

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Tips

1. केवल slow या external dependencies mock करें (email, payment, external APIs)।
2. DB और HTTP integration logic Feature tests में test database के साथ लिखें, heavy mocks नहीं।
3. `fakeApp()` के बाद हमेशा cleanup करें।

---

## संबंधित docs

- [Getting started with testing](./getting-started.md)
- [HTTP tests](./http-tests.md)
- [Console tests](./console-tests.md)
- [Portal](../basic/portal.md)
- [Services](../advanced/services.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
