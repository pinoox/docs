# boot.php e Events

[← Voltar ao índice](../README.md)

Além de `routes/`, você pode registrar rotas, endpoints de API, flows, schedules e listeners no **`boot.php`** — útil para **plugins**, micro-módulos ou hooks em um app hospedeiro (ex.: manager).

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
