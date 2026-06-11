# boot.php y eventos

[← Volver al índice](../README.md)

Además de `routes/`, puedes registrar rutas, endpoints API, flows, tareas programadas y listeners en **`boot.php`** — útil para **plugins**, micro-módulos o hooks en una app anfitriona (p. ej. manager).

---

## Tres modos de app

| Modo | Config | Comportamiento |
|------|--------|----------|
| **Solo rutas** | solo `router.routes` | Se ejecuta cuando la URL de la app está activa |
| **Boot global** | `boot-global => true` | Arranca en cada petición HTTP |
| **Boot + Rutas** | `boot.php` + rutas | Scaffold por defecto |

Plugin en una app anfitriona:

```php
'extends' => ['com_pinoox_manager'],
```

Tu plugin arranca solo cuando arranca el host (más ligero que global).

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

## AppRegister — métodos comunes

| Método | Propósito |
|--------|---------|
| `web(callable)` | Registrar rutas vía builder |
| `route([...])` | Ruta web única |
| `api([manifest])` | Manifiesto API completo |
| `apiRoute([...])` | Endpoint API único |
| `action('name', handler)` | Action con nombre |
| `flowAlias(['auth' => AuthFlow::class])` | Alias de flow |
| `schedule(callable)` | Tarea programada |
| `listen('event', listener)` | Listener de evento |
| `subscribe(SubscriberClass::class)` | Suscriptor Symfony |
| `when('com_host', fn)` | Hook cuando otra app arranca |

---

## Portal Event

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

Consulta [Correo](./mail.md) para desacoplar el envío de correo de los controllers.

**Flow** = antes del controller (middleware). **Event** = después de una acción (efectos secundarios).

---

## Helpers

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## Caché de boot

`'boot' => true` bajo `cache.stores` en `app.php` hornea el boot vía Pinker — consulta [Pinker](./pinker.md).

---

## Documentación relacionada

- [Programación](./schedule.md)
- [Flows](../basic/flows.md)
- [Routers](../basic/routers.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)
