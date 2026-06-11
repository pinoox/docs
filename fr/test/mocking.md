# Mocking dans Pinoox

[← Retour à l'index](../README.md)

Pour isoler l'unité testée des dépendances externes, utilisez **Pest + Mockery** (`mock()`) pour les classes et **`fakeApp()`** pour les apps temporaires. Les deux sont disponibles dans le bootstrap de test Pinoox.

---

## Mocker une classe — Unit

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

## Mocker Portal / service statique

Lorsqu'un Portal délègue à un Component, testez la logique du Component et mockez le Component — pas le Portal directement.

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // Injectez le mock dans le service testé
    });
});
```

---

## fakeApp — app temporaire (tests cœur)

Pour tester le router ou le boot sans créer une vraie app :

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

**Important :** n'oubliez jamais `deleteFakeApp()` dans `afterEach`.

---

## HTTP simulé — pas de mock manuel

Pour les vrais endpoints, utilisez `appGet` / `appPostJson` au lieu de mocker les contrôleurs — plus simple et plus proche du comportement en production.

---

## Spy — vérifier les appels

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... exécuter le code
```

---

## Exécuter les tests

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## Conseils

1. Mockez uniquement les dépendances lentes ou externes (e-mail, paiement, API externes).
2. Écrivez la logique d'intégration DB et HTTP en tests Feature avec une base de test, pas avec des mocks lourds.
3. Nettoyez toujours après `fakeApp()`.

---

## Documentation associée

- [Premiers pas avec les tests](./getting-started.md)
- [Tests HTTP](./http-tests.md)
- [Tests console](./console-tests.md)
- [Portal](../basic/portal.md)
- [Services](../advanced/services.md)

---

[← Retour à l'index](../README.md)
