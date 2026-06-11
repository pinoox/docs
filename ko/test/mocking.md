# Pinoox Mocking

[← 색인으로 돌아가기](../README.md)

test 대상을 외부 dependency에서 격리하려면 **Pest + Mockery**(`mock()`)로 class, **`fakeApp()`**로 임시 앱 사용. Pinoox test bootstrap에서 둘 다 사용 가능.

---

## Mock class — Unit

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

Portal이 Component에 위임하면 Component logic 테스트, Component mock — Portal 직접 mock 아님.

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

## fakeApp — 임시 앱 (core test)

real 앱 없이 router 또는 boot 테스트:

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

**Important:** `afterEach`에서 `deleteFakeApp()` 잊지 마세요.

---

## Fake HTTP — 수동 mock 없음

real endpoint에는 Controller mock 대신 `appGet` / `appPostJson` — 더 단순하고 production에 가까움.

---

## Spy — 호출 검증

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... run code
```

---

## Test 실행

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Tips

1. 느리거나 외부 dependency만 mock (email, payment, external API).
2. DB와 HTTP integration logic은 test database **Feature test**, heavy mock 아님.
3. `fakeApp()` 후 항상 cleanup.

---

## 관련 문서

- [테스트 시작하기](./getting-started.md)
- [HTTP tests](./http-tests.md)
- [Console tests](./console-tests.md)
- [Portal](../basic/portal.md)
- [Services](../advanced/services.md)

---

[← 색인으로 돌아가기](../README.md)
