# Pinoox 中的模拟（Mocking）

[← 返回索引](../README.md)

要将被测单元与外部依赖隔离，对类使用 **Pest + Mockery**（`mock()`），对临时应用使用 **`fakeApp()`**。两者均在 Pinoox 测试引导中可用。

---

## 模拟类 — Unit

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

## 模拟 Portal / 静态服务

当 Portal 委托给 Component 时，测试 Component 逻辑并模拟 Component — 不要直接模拟 Portal。

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // 将模拟注入被测服务
    });
});
```

---

## fakeApp — 临时应用（核心测试）

在不创建真实应用的情况下测试路由或引导：

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

**重要：** 切勿忘记在 `afterEach` 中调用 `deleteFakeApp()`。

---

## 伪造 HTTP — 无需手动 mock

对真实端点使用 `appGet` / `appPostJson`，不要模拟控制器 — 更简单且更接近生产行为。

---

## Spy — 验证调用

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... 运行代码
```

---

## 运行测试

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## 提示

1. 仅模拟慢或外部依赖（邮件、支付、外部 API）。
2. 数据库与 HTTP 集成逻辑写在 Feature 测试中并使用测试数据库，不要过度 mock。
3. `fakeApp()` 之后务必清理。

---

## 相关文档

- [测试入门](./getting-started.md)
- [HTTP 测试](./http-tests.md)
- [控制台测试](./console-tests.md)
- [Portal](../basic/portal.md)
- [服务](../advanced/services.md)

---

[← 返回索引](../README.md)
