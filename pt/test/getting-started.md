# Primeiros passos com testes no Pinoox

[← Voltar ao índice](../README.md)

O Pinoox usa uma abordagem única para o **núcleo do framework** (`tests/`) e **cada app** (`apps/{package}/tests/`): [Pest](https://pestphp.com/), bootstrap compartilhado e `AppTestKit`. Este guia percorre esse fluxo padrão com exemplos práticos.

---

## Stack de testes

| Ferramenta | Papel |
|------|------|
| Pest | Executar testes PHP |
| `Pinoox\Component\Test\AppTestKit` | Boot de ambiente, app temporário, requisições HTTP |
| `tests/bootstrap.php` | Ponto de entrada compartilhado para testes do núcleo e de apps |

---

## Executar testes

```bash
# Todos os testes do núcleo
vendor/bin/pest

# Pela CLI (seleção interativa de pacote)
php pinoox test

# Um app específico
php pinoox test com_my_shop

# Filtrar por nome de teste
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Apenas Feature ou Unit
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

No CI você também pode usar os scripts em `composer.json`:

```bash
composer test          # testes da plataforma
composer test:apps     # testes de todos os apps
```

---

## Estrutura de pasta de testes do app

Executar `php pinoox app:create` cria a pasta `tests/` automaticamente:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← smoke test
    └── Unit/
```

Criar um novo teste:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## O arquivo `tests/Pest.php`

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

O helper `appPackage()` define o app ativo para helpers e auto-detecção.

---

## Helpers globais

| Helper | Propósito |
|--------|---------|
| `appPackage($package?)` | Definir / ler o pacote ativo |
| `inApp($package, fn)` | Executar código dentro de `App::meeting()` |
| `appPath($package, $sub = '')` | Caminho para a pasta do app |
| `fakeApp($package, $files)` | Criar app temporário com arquivos personalizados |
| `deleteFakeApp($package)` | Remover app temporário |
| `appGet($package, $uri, ...)` | Requisição GET → `TestResponse` |
| `appPost($package, $uri, $data)` | Requisição POST |
| `appPostJson($package, $uri, $json)` | Requisição POST JSON |
| `pinooxBoot()` | Boot do ambiente de teste |

---

## Unit — testar uma classe Component

```php
// apps/com_my_shop/tests/Unit/PriceTest.php

it('calculates discount', function () {
    $package = appPackage();

    inApp($package, function () {
        $price = new App\com_my_shop\Component\PriceHelper();
        expect($price->discount(100, 10))->toBe(90);
    });
});
```

---

## Feature — smoke test de boot do app

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## Núcleo vs app

| Local | Propósito | Base case |
|----------|---------|-----------|
| `tests/Feature/` | Framework, portals, router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, integração | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, lógica pura | `Tests\AppTestCase` |

---

## Modo de teste

No ambiente de teste, `mode` é definido automaticamente como `test`:

```php
config('~pinoox')->get('mode'); // 'test'
```

No CI, configure `.env.testing` ou `APP_ENV=test` quando necessário.

---

## Dicas

1. Após `fakeApp()`, sempre chame `deleteFakeApp()` em `afterEach`.
2. Use `inApp()` para config, portals ou models dentro de um app.
3. Use `appGet` / `appPostJson` para rotas e APIs.
4. Rotas → **Feature**; classes `Component/` → **Unit**.
5. Use `php pinoox test:create` em vez de copiar arquivos manualmente.

---

## Documentação relacionada

- [Testes HTTP](./http-tests.md)
- [Testes de console](./console-tests.md)
- [Testes de browser (HTML)](./browser-tests.md)
- [Testes de banco de dados](./database.md)
- [Mocking](./mocking.md)
- [Seu primeiro app](../start/your-first-app.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)
