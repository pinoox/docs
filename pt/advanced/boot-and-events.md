# boot.php e Events

[← Voltar ao índice](../README.md)

Além de `routes/`, você pode registrar rotas, endpoints de API, flows, schedules e listeners no **`boot.php`** — útil para **plugins**, micro-módulos ou hooks em um app hospedeiro (ex.: manager).

Cada app pode ter `apps/{package}/boot.php`. O ficheiro devolve um closure que recebe `AppRegister` e corre **antes** de tratar o pedido.

---

## Ciclo de vida

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### Etapas do pipeline

| Etapa | Propósito |
|-------|-----------|
| `boot.global` | Boot de apps com `boot-global => true` em cada pedido |
| `app.boot` | Boot da app activa (+ extenders via `extends`) |

### Eventos de boot

| Nome | Quando |
|------|--------|
| `app.booting` / `app.booting.{package}` | Antes do commit |
| `app.booted` / `app.booted.{package}` | Depois de integrate |
| `app.routes` / `app.routes.{package}` | Ao aplicar rotas web |
| `app.api` / `app.api.{package}` | Ao construir registry API |

Ouvir a partir de `boot.php`:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### Eventos de pedido (núcleo)

Disparados automaticamente em cada pedido HTTP pelo framework (`AppCoreEventSubscriber`):

| Nome | Quando | Variante package | Canal nomeado |
|------|--------|------------------|---------------|
| `app.route.matched` | Após match de rota | `app.route.matched.{package}` | `app.route.{routeName}` ou `app.api.{routeName}` |
| `app.controller` | Antes do controller | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | Antes de enviar resposta | `app.response.{package}` | — |
| `app.exception` | Excepção não tratada | `app.exception.{package}` | — |
| `app.terminate` | Após resposta enviada | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

Use **watches** (`onRoute`, `onApi`, …) para hooks simples; **listen** nos eventos do núcleo para controlo total ou plugins cross-app.

---

## Três modos de app

| Modo | Configuração | Comportamento |
|------|--------|----------|
| **Somente rota** | apenas `router.routes` | Executa quando a URL do app está ativa |
| **Boot global** | `boot-global => true` | Inicializa em toda requisição HTTP |
| **Boot + Route** | `boot.php` + rotas | Scaffold padrão |

Plugin em um app hospedeiro:

```php
'extends' => ['com_host_app'],
```

Seu plugin inicializa apenas quando o hospedeiro inicializa (mais leve que o global).

---

## chaves `app.php` para boot

Estas chaves em `apps/{package}/app.php` controlam **se** `boot.php` executa, **quando** e se a saída é cacheada. Configuram a pipeline de boot — não substituem `boot.php`.

### Ficheiro boot (`boot`)

| Valor | Padrão | Efeito |
|-------|--------|--------|
| `true` | sim | Executar `boot.php` ao bootar a app |
| `false` | | Sem boot — só rotas |
| `'path/custom.php'` | | Outro ficheiro relativo à raiz da app |

O ficheiro deve **retornar um callable** `fn (AppRegister $register) => …`.

### Plugin global (`boot-global`)

| Valor | Padrão | Efeito |
|-------|--------|--------|
| `false` | sim | Boot só quando esta app está activa |
| `true` | | Boot em **cada pedido HTTP** |

### Plugin no host (`extends`)

| Valor | Padrão | Efeito |
|-------|--------|--------|
| `[]` | sim | App normal |
| `['com_host_app']` | | Boot **antes** do host quando activo |

### Registo extra (`startup`)

Callable opcional em `app.php`, **depois** de `boot.php`.

### Cache boot (`cache`)

Opt-in: `cache.enabled` deve ser `true`. Após deploy: `php pinoox cache:build {package}`.

### Escolha rápida

| Objetivo | Definir |
|----------|---------|
| App normal | `'boot' => true` |
| Só rotas | `'boot' => false` |
| Plugin global | `'boot-global' => true` |
| Plugin no host | `'extends' => ['com_host_app']` |

---

## boot.php básico

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## AppRegister — métodos comuns

| Método | Finalidade |
|--------|---------|
| `web(callable)` | Registrar rotas via builder |
| `route([...])` | Rota web única |
| `api([manifest])` | Manifest completo da API |
| `apiRoute([...])` | Endpoint de API único |
| `action('name', handler)` | Action nomeada |
| `flowAlias(['auth' => AuthFlow::class])` | Alias de Flow |
| `schedule(callable)` | Tarefa agendada |
| `listen('event', listener)` | Listener de event |
| `subscribe(SubscriberClass::class)` | Subscriber do Symfony |
| `when('com_host', fn)` | Hook quando outro app inicializa |

---

## Portal Event

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Veja [E-mail](./mail.md) para desacoplar o envio de e-mail dos controllers.

**Flow** = antes do controller (middleware). **Event** = depois de uma action (efeitos colaterais).

---

## Helpers

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Cache de boot

`'boot' => true` em `cache.stores` no `app.php` compila o boot via Pinker — veja [Pinker](./pinker.md).

---

## Documentação relacionada

- [Schedule](./schedule.md)
- [Flows](../basic/flows.md)
- [Routers](../basic/routers.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)
