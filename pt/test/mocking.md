# Mocking no Pinoox

[← Voltar ao índice](../README.md)

Para isolar a unidade sob teste de dependências externas, use **Pest + Mockery** (`mock()`) para classes e **`fakeApp()`** para apps temporários. Ambos estão disponíveis no bootstrap de testes do Pinoox.

---

## Mock de uma classe — Unit

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

## Mock de Portal / serviço estático

Quando um Portal delega a um Component, teste a lógica do Component e faça mock do Component — não do Portal diretamente.

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // Injete o mock no serviço sob teste
    });
});
```

---

## fakeApp — app temporário (testes do núcleo)

Para testar router ou boot sem criar um app real:

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

**Importante:** nunca esqueça `deleteFakeApp()` em `afterEach`.

---

## HTTP falso — sem mock manual

Para endpoints reais, use `appGet` / `appPostJson` em vez de mockar controllers — mais simples e mais próximo do comportamento em produção.

---

## Spy — verificar chamadas

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... executar código
```

---

## Executar testes

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Dicas

1. Faça mock apenas de dependências lentas ou externas (e-mail, pagamento, APIs externas).
2. Escreva lógica de integração DB e HTTP em testes Feature com banco de teste, não mocks pesados.
3. Sempre limpe após `fakeApp()`.

---

## Documentação relacionada

- [Primeiros passos com testes](./getting-started.md)
- [Testes HTTP](./http-tests.md)
- [Testes de console](./console-tests.md)
- [Portal](../basic/portal.md)
- [Serviços](../advanced/services.md)

---

[← Voltar ao índice](../README.md)
