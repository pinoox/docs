# Referencia del manifiesto app.php

[← Volver al índice](../README.md)

`app.php` es el manifiesto de tu app. Los valores predeterminados viven en `vendor/pinoox/pincore/Component/Package/data/source.php` — sobrescribe solo lo que necesites.

---

## Identidad y activación

| Clave | Propósito |
|-----|---------|
| `package` | Nombre de la carpeta = namespace (`com_acme_shop`) |
| `name` | Nombre para mostrar |
| `enable` | Habilita / deshabilita la app |
| `description`, `developer`, `icon` | Metadatos |
| `version-name`, `version-code` | Versión de la app |
| `sys-app`, `hidden`, `dock` | App de sistema / oculta / dock del manager |
| `minpin` | Versión mínima de la plataforma |

---

## Router y boot

| Clave | Propósito |
|-----|---------|
| `router.routes` | Archivos `routes/*.php` |
| `boot` | Ejecuta `boot.php` (true por defecto) |
| `boot-global` | Boot en cada petición HTTP |
| `extends` | Boot cuando arranca la app anfitriona |
| `loader` | Archivos extra (`func.php`) |
| `depends` | Apps requeridas |

Consulta [boot.php y eventos](../advanced/boot-and-events.md).

---

## Flow y seguridad

| Clave | Propósito |
|-----|---------|
| `flow` | Flows globales (BootFlow) |
| `alias` | Nombre → clase Flow |
| `auth` | modo, lifetime, JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | Comparte usuario/archivos/acceso con la plataforma |

Consulta [Flows](../basic/flows.md), [Gestión de usuarios](../advanced/user-management.md), [Acceso](../advanced/access-permissions.md).

---

## UI y tema

| Clave | Propósito |
|-----|---------|
| `theme` | Carpeta del tema activo |
| `theme-context`, `theme-contexts`, `theme-extends` | Multicontexto / herencia |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | Locale predeterminado |
| `open` | Comportamiento de apertura en el manager |

---

## Base de datos y almacenamiento

| Clave | Propósito |
|-----|---------|
| `database` | Sobrescritura de la conexión a la BD |
| `table.prefix` | Prefijo de tablas |
| `transport.user` / `file_storage` / `access` | Presets o claves granulares |
| `filesystem` | disk, hash_length, dispatcher, file_policy, groups, thumbs |

---

## Runtime

| Clave | Propósito |
|-----|---------|
| `runtime.mode`, `runtime.debug` | Sobrescrituras del modo |
| `cache` | Bake de routes/api/boot/twig |
| `log`, `redis`, `date` | Sobrescrituras por app |
| `container` | Bindings de DI |

---

## Pinker / Pinx

| Clave | Propósito |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | exclude/include para paquetes |

---

## Ejemplo combinado

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## Documentación relacionada

- [Estructura del proyecto](./structure.md)
- [Configuración (Config)](../basic/config.md)

---

[← Volver al índice](../README.md)
