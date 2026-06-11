# Mocking в Pinoox

[← Вернуться к оглавлению](../README.md)

Чтобы изолировать тестируемую единицу от внешних зависимостей, используйте **Pest + Mockery** (`mock()`) для классов и **`fakeApp()`** для временных приложений. Оба доступны в bootstrap тестов Pinoox.

---

## Mock класса — Unit

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

## Mock Portal / статического сервиса

Когда Portal делегирует Component, тестируйте логику Component и мокайте Component — не Portal напрямую.

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // Внедрите mock в тестируемый сервис
    });
});
```

---

## fakeApp — временное приложение (тесты ядра)

Для тестирования router или boot без создания реального приложения:

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

**Важно:** никогда не забывайте `deleteFakeApp()` в `afterEach`.

---

## Fake HTTP — без ручного mock

Для реальных endpoint используйте `appGet` / `appPostJson` вместо моков контроллеров — проще и ближе к production-поведению.

---

## Spy — проверка вызовов

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... run code
```

---

## Запуск тестов

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Советы

1. Мокайте только медленные или внешние зависимости (email, payment, внешние API).
2. Логику интеграции БД и HTTP пишите в Feature-тестах с тестовой БД, а не с тяжёлыми mocks.
3. Всегда очищайте после `fakeApp()`.

---

## Связанные документы

- [Начало работы с тестированием](./getting-started.md)
- [HTTP-тесты](./http-tests.md)
- [Тесты консоли](./console-tests.md)
- [Portal](../basic/portal.md)
- [Services](../advanced/services.md)

---

[← Вернуться к оглавлению](../README.md)
