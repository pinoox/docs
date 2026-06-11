# Referencia de la CLI de Pinoox

[← Volver al índice](../README.md)

Ejecuta todos los comandos desde la **raíz del proyecto**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Cuando se requiere un paquete y se omite, Pinoox muestra un selector interactivo.

> Para proyectos de **una sola app**, usa la [Pinx CLI](./pinx-cli.md) independiente (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Alias comunes

| Alias | Comando |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## Apps

| Comando | Propósito |
|---------|---------|
| `app:create {package}` | Generar app (`--simple`, `--stack`, `--profile`) |
| `app:list` | Listar apps |
| `app:delete` | Eliminar app |
| `app:router set /path {package}` | Mapeo de URL |
| `app:domain` | Mapa host → app |
| `app:resolve` | Depurar la app activa |

---

## Scaffolding (generación de código)

| Comando | Salida |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | Clase FormRequest |
| `seeder:create` | `database/seed/` |
| `test:create` | Archivo Pest |
| `theme:frontend` | Herramientas de frontend (Vue/React/Twig) |

---

## Base de datos

| Comando | Propósito |
|---------|---------|
| `migrate {package}` | Ejecutar migraciones (app, `platform`, `pincore`) |
| `migrate:create` | Nuevo archivo de migración |
| `migrate:status` / `migrate:rollback` | Estado / rollback |
| `seeder:run` | Ejecutar seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | SQL directo (depuración) |

---

## Caché y Pinker

| Comando | Propósito |
|---------|---------|
| `cache:build` / `cache:clear` | Caché de runtime |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Restablecer Pinker + configuración |

---

## Schedule

| Comando | Propósito |
|---------|---------|
| `schedule:list` | Listar tareas cron |
| `schedule:run` | Ejecutar tareas pendientes |

Consulta [Schedule](../advanced/schedule.md).

---

## Router

| Comando | Propósito |
|---------|---------|
| `route:actions {package}` | Listar Named Actions |

---

## Empaquetado Pinx

| Comando | Propósito |
|---------|---------|
| `pinx:build` | Construir paquete `.pinx` |
| `pinx:install` | Instalar paquete |
| `pinx:info` | Metadatos |
| `wizard:list` / `wizard:install` | Asistente de instalación |

---

## Desarrollo

| Comando | Propósito |
|---------|---------|
| `test` | Pruebas con Pest |
| `serve` | Servidor de desarrollo integrado |
| `log:view` / `log:clear` | Logs |
| `deps` | Composer/npm en todas las apps |
| `version` / `mode:show` | Versión / modo de runtime |

---

## Argumento de paquete

| Valor | Significado |
|-------|---------|
| `com_my_shop` | App específica |
| `platform` | Migraciones/patches/seeders de la plataforma |
| `pincore` | Núcleo del framework |
| `all` | Todas las apps (cache/pinker) |

---

## Documentación relacionada

- [Tu primera app](./your-first-app.md)
- [Migraciones (Migrations)](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← Volver al índice](../README.md)
