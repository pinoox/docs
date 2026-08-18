# Mocking in Pinoox

[← Back to index](../README.md)

To isolate the unit under test from external dependencies, use **Pest + Mockery** (`mock()`) for classes and **`fakeApp()`** for temporary apps. Both are available in the Pinoox test bootstrap.

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

When a Portal delegates to a Component, test the Component logic and mock the Component — not the Portal directly.

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

To test router or boot without creating a real app:

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

**Important:** never forget `deleteFakeApp()` in `afterEach`.

---

## Fake HTTP — no manual mock

For real endpoints, use `appGet` / `appPostJson` instead of mocking controllers — simpler and closer to production behavior.

---

## Spy — verify calls

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... run code
```

---

## Fake events

Record dispatches without running listeners. Full guide: [Events](../advanced/events.md#testing).

```php
use App\com_acme_shop\Event\OrderPlaced;
use Pinoox\Portal\Event;

it('dispatches OrderPlaced on checkout', function () {
    event_fake(OrderPlaced::class);

    // … run checkout …

    Event::assertDispatched(OrderPlaced::class);
    Event::dontFake();
});
```

`event_fake()` with no arguments fakes **all** events (including kernel events). Prefer a class or name list, and always call `Event::dontFake()` when the test finishes.

---

## Running tests

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Tips

1. Mock only slow or external dependencies (email, payment, external APIs).
2. Write DB and HTTP integration logic in Feature tests with a test database, not heavy mocks.
3. Always clean up after `fakeApp()`.

---

## Related docs

- [Getting started with testing](./getting-started.md)
- [HTTP tests](./http-tests.md)
- [Console tests](./console-tests.md)
- [Portal](../basic/portal.md)
- [Events](../advanced/events.md)
- [Services](../advanced/services.md)

---

[← Back to index](../README.md)
