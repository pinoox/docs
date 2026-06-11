# Pinker y caché

[← Volver al índice](../README.md)

**Pinker** es la capa bake/runtime en Pinoox 3.x: la config y la caché se compilan desde el origen en archivos PHP que pueden `include`arse para un arranque más rápido. Ruta estándar por app: **`pinker/apps/{package}/`**.

---

## Estructura de carpetas

```
pinker/
└── apps/
    └── com_acme_shop/
        ├── app.php              ← app.php horneado
        └── cache/
            ├── manifest.php     ← checksum + built_at
            ├── routes.php
            ├── api.php
            ├── boot.php
            └── twig/             ← plantillas compiladas
```

A nivel de proyecto:

```
pinker/config/          ← config horneada (no sensible al entorno)
pinker/state/config/    ← sobrescrituras post-instalación (p. ej. database)
```

---

## Comandos CLI

```bash
# Reconstruir Pinker para una app
php pinoox pinker:rebuild com_acme_shop

# Alias corto
php pinoox bake com_acme_shop

# Estado: comparar origen vs salida horneada
php pinoox pinker:status com_acme_shop

# Construir caché (route, api, twig, pinker, …)
php pinoox cache:build com_acme_shop

# Solo Twig
php pinoox cache:build com_acme_shop --only=twig

# Solo Pinker
php pinoox cache:build com_acme_shop --only=pinker

# Limpiar caché
php pinoox cache:clear com_acme_shop
```

---

## Cuándo reconstruir

| Evento | Comando |
|-------|---------|
| Cambiar `app.php` o config | `pinker:rebuild` |
| Cambiar route / api | `cache:build` |
| Cambiar `.twig` en producción | `cache:build --only=twig` |
| Tras instalar en servidor | `cache:build` + `pinker:rebuild` |
| Antes de construir `.pinx` | `cache:build` (caché dentro del paquete) |

---

## Habilitar caché en runtime

En `apps/{package}/app.php`:

```php
'cache' => [
    'enabled' => false,   // por defecto — pon true en producción si hace falta
    'stores' => [
        'routes' => true,
        'api' => true,
        'boot' => true,
        'twig' => true,
        'pinker' => true,
    ],
    'build' => [
        'include_in_package' => true,
    ],
],
```

---

## Espejo de app — `pinker/app.php`

Cada app puede tener un espejo horneado:

```
apps/com_acme_shop/pinker/app.php   ← origen/referencia en el repo
         ↓ bake
pinker/apps/com_acme_shop/app.php   ← runtime
```

---

## Helper `pinker()`

Para horneado manual de datos:

```php
pinker($data, ['lifetime' => 3600]);
```

Normalmente usas la CLI; rara vez hace falta en código de app.

---

## Flujo de despliegue recomendado

```bash
# 1. construir frontend
php pinoox theme:frontend build com_acme_shop

# 2. caché
php pinoox cache:build com_acme_shop

# 3. pinker (específico del entorno)
php pinoox pinker:rebuild com_acme_shop
```

---

## Consejos

- No edites `pinker/state/` manualmente — el instalador escribe ahí.
- En desarrollo la caché de runtime suele estar desactivada; reconstruye solo tras cambios importantes.
- `.pinx` puede incluir caché preconstruida; en el servidor destino ejecuta `cache:build --only=pinker` una vez.

---

## Documentación relacionada

- [Config](../basic/config.md)
- [Plantillas Twig](../basic/templates.md)
- [Referencia CLI](../start/cli-reference.md)
- [Router](../basic/routers.md)

---

[← Volver al índice](../README.md)
