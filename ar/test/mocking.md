# Mocking في Pinoox

[← العودة إلى الفهرس](../README.md)

لعزل الوحدة تحت الاختبار عن التبعيات الخارجية، استخدم **Pest + Mockery** (`mock()`) للفئات و**`fakeApp()`** للتطبيقات المؤقتة. كلاهما متاح في bootstrap اختبار Pinoox.

---

## Mock لفئة — Unit

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

## Mock Portal / خدمة ثابتة

عندما يفوّض Portal إلى Component، اختبر منطق Component وmock الـ Component — وليس Portal مباشرة.

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

## fakeApp — تطبيق مؤقت (اختبارات النواة)

لاختبار router أو boot دون إنشاء تطبيق حقيقي:

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

**مهم:** لا تنسَ `deleteFakeApp()` في `afterEach` أبدًا.

---

## HTTP وهمي — بدون mock يدوي

لنقاط النهاية الحقيقية، استخدم `appGet` / `appPostJson` بدل mock المتحكمات — أبسط وأقرب لسلوك الإنتاج.

---

## Spy — التحقق من الاستدعاءات

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... run code
```

---

## تشغيل الاختبارات

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## نصائح

1. Mock التبعيات البطيئة أو الخارجية فقط (بريد، دفع، APIs خارجية).
2. اكتب منطق تكامل DB وHTTP في Feature tests مع قاعدة اختبار، وليس mocks ثقيلة.
3. نظّف دائمًا بعد `fakeApp()`.

---

## وثائق ذات صلة

- [البدء مع الاختبار](./getting-started.md)
- [اختبارات HTTP](./http-tests.md)
- [اختبارات Console](./console-tests.md)
- [Portal](../basic/portal.md)
- [الخدمات (Services)](../advanced/services.md)

---

[← العودة إلى الفهرس](../README.md)
