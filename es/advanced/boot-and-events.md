# boot.php y eventos

[← Volver al índice](../README.md)

Además de `routes/`, puedes registrar rutas, endpoints API, flows, tareas programadas y listeners en **`boot.php`** — útil para **plugins**, micro-módulos o hooks en una app anfitriona (p. ej. manager).

Cada app puede incluir `apps/{package}/boot.php`. El archivo devuelve un closure que recibe `AppRegister` y se ejecuta **antes** de manejar la petición.

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

### Etapas del pipeline

| Etapa | Propósito |
|-------|-----------|
| `boot.global` | Boot de apps con `boot-global => true` en cada petición |
| `app.boot` | Boot de la app activa (+ extenders vía `extends`) |

### Eventos de boot

| Nombre | Cuándo |
|--------|--------|
| `app.booting` / `app.booting.{package}` | Antes del commit |
| `app.booted` / `app.booted.{package}` | Tras integrate |
| `app.routes` / `app.routes.{package}` | Al aplicar rutas web |
| `app.api` / `app.api.{package}` | Al construir el registry API |

Escuchar desde `boot.php`:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### Eventos de petición (núcleo)

Disparados automáticamente en cada petición HTTP por el framework (`AppCoreEventSubscriber`):

| Nombre | Cuándo | Variante package | Canal nombrado |
|--------|--------|------------------|----------------|
| `app.route.matched` | Tras match de ruta | `app.route.matched.{package}` | `app.route.{routeName}` o `app.api.{routeName}` |
| `app.controller` | Antes del controller | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | Antes de enviar respuesta | `app.response.{package}` | — |
| `app.exception` | En excepción no capturada | `app.exception.{package}` | — |
| `app.terminate` | Tras enviar respuesta | `app.terminate.{package}` | — |

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

Usa **watches** (`onRoute`, `onApi`, …) para hooks simples; **listen** en eventos del núcleo para control total o plugins cross-app.

---

## Tres modos de app

| Modo | Config | Comportamiento |
|------|--------|----------|
| **Solo rutas** | solo `router.routes` | Se ejecuta cuando la URL de la app está activa |
| **Boot global** | `boot-global => true` | Arranca en cada petición HTTP |
| **Boot + Rutas** | `boot.php` + rutas | Scaffold por defecto |

Plugin en una app anfitriona:

```php
'extends' => ['com_host_app'],
```

Tu plugin arranca solo cuando arranca el host (más ligero que global).

---

## claves de `app.php` para boot

Estas claves en `apps/{package}/app.php` controlan **si** se ejecuta `boot.php`, **cuándo** y si se cachea la salida. Configuran la pipeline de boot — no sustituyen `boot.php`.

### Archivo boot (`boot`)

| Valor | Por defecto | Efecto |
|-------|-------------|--------|
| `true` | sí | Ejecutar `boot.php` al bootear la app |
| `false` | | Sin boot — solo rutas |
| `'path/custom.php'` | | Otro archivo relativo a la raíz de la app |

El archivo debe **devolver un callable** `fn (AppRegister $register) => …`. Si falta con `true`, el boot continúa sin error.

### Plugin global (`boot-global`)

| Valor | Por defecto | Efecto |
|-------|-------------|--------|
| `false` | sí | Boot solo cuando esta app está activa |
| `true` | | Boot en **cada petición HTTP** |

### Plugin en host (`extends`)

| Valor | Por defecto | Efecto |
|-------|-------------|--------|
| `[]` | sí | App normal |
| `['com_host_app']` | | Boot **antes** del host cuando se activa |

### Registro extra (`startup`)

Callable opcional en `app.php`, **después** de `boot.php`, misma API `AppRegister`.

### Caché boot (`cache`)

Opt-in: `cache.enabled` debe ser `true`.

| Clave | Por defecto | Efecto |
|-------|-------------|--------|
| `cache.enabled` | `false` | Interruptor principal |
| `cache.stores.boot` | `true` | Cache de registros boot |
| `cache.stores.routes` | `true` | Cache de manifests de rutas |
| `cache.stores.api` | `true` | Cache de listas API |

Tras desplegar: `php pinoox cache:build {package}`.

### Guía rápida

| Objetivo | Ajuste |
|----------|--------|
| App normal | `'boot' => true` |
| Solo rutas | `'boot' => false` |
| Plugin global | `'boot-global' => true` |
| Plugin en host | `'extends' => ['com_host_app']` |
| Boot prod más rápido | `'cache.enabled' => true` |

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

## Theme — contextos, herencia y hooks en boot

Carpetas en `apps/{package}/theme/{name}/`. Theme activo en **`app.php`**; hooks en **`boot.php`**.

### Claves en `app.php`

| Clave | Propósito |
|-------|-----------|
| `theme` | Carpeta theme activa |
| `theme-context` / `theme-contexts` | Varios themes (site / panel / …) |
| `theme-extends` | Herencia |
| `path-theme` | Ruta custom |
| `frontend` | Vite profile, entry, manifest |

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

Routes: `flows: ['auth', 'theme.panel']`. En `theme/{name}/`: `theme.php`, Twig, `functions.php`, `frontend.config.php`, `src/` / `dist/`.

Ver [Views](../basic/views.md), [Twig](../basic/templates.md), [app.php](../start/app-manifest.md).

### Desde `boot.php`

**`onTheme`** o **listen** / **watch**:

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});
```

En controller: `View::changeTheme('panel')`, `ThemeContext::activate('panel')`, `within_theme(...)`.

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

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
