# Pinoox'ta mocking

[← Dizine dön](../README.md)

Test edilen birimi harici bağımlılıklardan izole etmek için sınıflar için **Pest + Mockery** (`mock()`) ve geçici uygulamalar için **`fakeApp()`** kullanın. İkisi de Pinoox test bootstrap'ında mevcuttur.

---

## Sınıf mock'lama — Unit

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

## Portal / statik servis mock'lama

Bir Portal bir Component'e delege ettiğinde Component mantığını test edin ve Component'i mock'layın — Portal'ı doğrudan değil.

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

## fakeApp — geçici uygulama (çekirdek testleri)

Router veya boot'u gerçek uygulama oluşturmadan test etmek için:

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

**Önemli:** `afterEach` içinde `deleteFakeApp()`'i asla unutmayın.

---

## Sahte HTTP — manuel mock yok

Gerçek endpoint'ler için controller'ları mock'lamak yerine `appGet` / `appPostJson` kullanın — daha basit ve üretime daha yakın.

---

## Spy — çağrıları doğrulama

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... run code
```

---

## Testleri çalıştırma

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## İpuçları

1. Yalnızca yavaş veya harici bağımlılıkları mock'layın (e-posta, ödeme, harici API'ler).
2. DB ve HTTP entegrasyon mantığını test veritabanıyla Feature testlerinde yazın, ağır mock kullanmayın.
3. `fakeApp()` sonrası her zaman temizlik yapın.

---

## İlgili dokümantasyon

- [Teste başlarken](./getting-started.md)
- [HTTP testleri](./http-tests.md)
- [Konsol testleri](./console-tests.md)
- [Portal](../basic/portal.md)
- [Servisler](../advanced/services.md)

---

[← Dizine dön](../README.md)
