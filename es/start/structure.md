# Estructura del proyecto

[← Volver al índice](../README.md)

Pinoox usa la arquitectura HMVC: cada app bajo `apps/{package}/` es un módulo MVC completo e independiente. El núcleo del framework vive en `vendor/pinoox/pincore/` y se edita solo cuando se modifica la plataforma misma.

---

## Estructura del proyecto

```
{project_root}/
├── index.php
├── pinoox
├── composer.json
├── vendor/pinoox/pincore/   ← núcleo (paquete de Composer)
├── apps/                    ← todas las apps
│   ├── com_pinoox_manager/
│   └── com_acme_shop/
├── config/
└── storage/                 ← archivos subidos y almacenamiento de apps
```

---

## Estructura de una app

```
apps/com_acme_shop/
├── app.php                  ← manifiesto (obligatorio)
├── boot.php                 ← rutas/eventos programáticos (opcional)
├── schedule.php             ← tareas cron (opcional)
├── Controller/              ← manejadores HTTP
├── Model/                   ← modelos Eloquent
├── Flow/                    ← middleware
├── Component/               ← lógica de negocio
├── Portal/                  ← facades de la app (opcional)
├── routes/
│   ├── web.php
│   ├── actions.php
│   └── api.php
├── Router/                  ← constantes de nombres de acciones (opcional)
├── theme/default/           ← Twig + assets
├── lang/en/                 ← traducciones
├── config/                  ← configuración de la app
├── database/migrations/
└── pinker/                  ← espejo de build
```

Las vistas no están en una carpeta `View/` separada — las plantillas viven en `theme/{themeName}/`.

---

## app.php — campos clave

```php
<?php

return [
    'package' => 'com_acme_shop',   // = nombre de la carpeta
    'name' => 'Shop',
    'enable' => true,
    'theme' => 'default',
    'flow' => [
        App\com_acme_shop\Flow\BootFlow::class,
    ],
    'alias' => [
        'auth' => App\com_acme_shop\Flow\AuthFlow::class,
    ],
    'router' => [
        'routes' => [
            'routes/web.php',
            'routes/actions.php',
        ],
    ],
];
```

---

## Namespaces

PSR-4: `App\` → `apps/`

| Archivo | Namespace |
|------|-----------|
| `apps/com_acme_shop/Controller/OrderController.php` | `App\com_acme_shop\Controller` |
| `apps/com_acme_shop/Model/OrderModel.php` | `App\com_acme_shop\Model` |
| `apps/com_acme_shop/Flow/AuthFlow.php` | `App\com_acme_shop\Flow` |

---

## Reglas de nomenclatura

- Paquete: `com_{vendor}_{name}` — p. ej. `com_acme_shop`
- Nombre de la carpeta = `package` en `app.php` = segmento del namespace
- Prefijo de tablas de BD: `{package}_` (p. ej. `com_acme_shop_orders`)

---

## Límite entre app y núcleo

| Cambio | Ubicación |
|--------|----------|
| Nuevo endpoint | `apps/{package}/Controller/` + `routes/` |
| Migración | `apps/{package}/database/migrations/` |
| Bug del framework | `pinoox/pincore` (upstream) |
| UI | `apps/{package}/theme/` |

Mantén las apps independientes — usa las facades `Pinoox\Portal\*` en lugar de acoplar las apps entre sí.

---

## Documentación relacionada

- [Tu primera app](./your-first-app.md)
- [Router](../basic/routers.md)
- [Controllers](../basic/controllers.md)
- [Flow](../basic/flows.md)

---

[← Volver al índice](../README.md)
