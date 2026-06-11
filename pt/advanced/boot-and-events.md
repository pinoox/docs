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
'extends' => ['com_pinoox_manager'],
```

Seu plugin inicializa apenas quando o hospedeiro inicializa (mais leve que o global).

---

## boot.php básico

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => response()->json(['ok' => true]),
        'name' => 'health',
    ]);

    $register->when('com_pinoox_manager', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => response()->json(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['manager.auth'],
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
