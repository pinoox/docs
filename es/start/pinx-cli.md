# Pinx CLI (proyectos de una sola app)

[← Volver al índice](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** es la CLI para desarrolladores de proyectos Pinoox de **una sola app** — genera, ejecuta, migra, construye y publica paquetes `.pinx` sin tocar un manager multi-app.

Está construida sobre `pinoox/pincore` y la plantilla `pinoox/app`. La raíz de tu proyecto **es** la app: un `app.php`, un paquete, un flujo de trabajo.

> Para instalaciones clásicas de plataforma multi-app, usa [`php pinoox`](./cli-reference.md) en su lugar.

---

## Inicio rápido

Instala Pinx una vez, crea una nueva app y ejecútala:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # sugiere com_my_shop — confirma o edita en el asistente
cd my-shop
cp .env.example .env          # configura DB_* si usas una base de datos
pinx setup                    # migra la plataforma + la app, ejecuta los seeders
pinx dev                      # http://127.0.0.1:8000
```

Agrega el `bin` global de Composer a tu `PATH` si `pinx` no se encuentra:

- Linux / macOS: `~/.composer/vendor/bin` o `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| Paso | Qué hace |
|------|--------------|
| `composer global require` | Instala el comando `pinx` en tu máquina |
| `pinx new my-shop` | Genera desde `pinoox/app`; el asistente sugiere un paquete de 3 partes (p. ej. `com_my_shop`) |
| `.env` | Base de datos y rutas del proyecto — copia desde `.env.example` |
| `pinx setup` | De una sola vez: migraciones de plataforma → migraciones de la app → seeders |
| `pinx dev` | Servidor de desarrollo PHP; también inicia Vite cuando hay un stack de frontend configurado |

Los nombres de paquete siguen `com_{vendor}_{name}` — p. ej. `com_acme_shop`, `ir_yekdo_app`. ¿Ya estás dentro de una carpeta vacía? Usa `pinx init` en lugar de `pinx new`.

**Comprobación opcional antes de `setup`:** `pinx doctor` informa sobre PHP, la estructura, el entorno (env), la base de datos y la preparación del build.

---

## Alternativa: `composer create-project`

Sin instalación global — la plantilla incluye `bin/pinx` dentro del proyecto:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## Qué hace diferente al modo de una sola app

Las instalaciones clásicas de Pinoox mantienen muchas apps bajo `apps/` y eligen una en tiempo de ejecución. El modo de **una sola app** aplana eso:

- `app.php` en la raíz del proyecto contiene la identidad del paquete y la configuración de pinx
- `Controller/`, `Model/`, `routes/`, `theme/` viven en la raíz — no dentro de `apps/{package}/`
- `platform/` contiene el enrutamiento local y la configuración del launcher (excluido de los builds `.pinx`)
- Pinx siempre apunta a **tu** app — sin selector de paquetes, sin interfaz de manager

```
my-shop/                    ← raíz del proyecto = raíz de la app
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← host de desarrollo + capa de despliegue (solo local)
├── bin/pinx                ← entrada de CLI local al proyecto
└── vendor/pinoox/pincore   ← framework
```

---

## Opciones de instalación

| Dónde | Cómo | Cuándo usarlo |
|-------|-----|-------------|
| **Global** | `composer global require pinoox/pinx-cli` | Recomendado — `pinx new` y `pinx init` desde cualquier lugar |
| **Por proyecto** | Incluido como `bin/pinx` en `pinoox/app` | Después de `composer create-project` — sin instalación global |

```bash
pinx -v          # versión de la CLI (p. ej. pinx-cli 1.1.7)
pinx list        # resumen de comandos agrupados
pinx help setup  # detalle de un comando
```

---

## Flujo de trabajo diario

```bash
pinx dev                    # servidor local (+ Vite cuando app.php → frontend.stack está configurado)
pinx dev --open             # abre el navegador después de iniciar
pinx dev --no-frontend      # solo PHP

pinx migrate                # ejecuta las migraciones de la app (--platform ejecuta primero la plataforma)
pinx migrate:st             # estado de las migraciones
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # lista las named actions (--validate, --json)
pinx test                   # ejecuta las pruebas de la app (Pest)
```

**Frontend** (cuando `theme/` usa Vue/React + Vite):

```bash
pinx fe:info                # stack, scripts de npm, rutas
pinx fe:i                   # npm install
pinx fe:d                   # servidor de desarrollo de Vite
pinx fe:b                   # build de producción
pinx fe:sc --stack=vue      # genera archivos iniciales
```

**Dependencias:**

```bash
pinx deps:st                # estado de Composer + npm
pinx deps:i                 # instala todo
pinx deps:up                # actualiza todo
```

**Pinker** (caché de build):

```bash
pinx pinker:st              # caché vs fuente
pinx pinker:rb              # reconstruir
pinx pinker:df              # diferencias
```

---

## Publicar a producción

Construye un paquete `.pinx` para instalarlo en una plataforma Pinoox completa (Manager → Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # incrementa la versión en app.php + build
pinx release --sign         # firma cuando hay una clave configurada en app.php → pinx.sign
```

`pinx build` aplica valores predeterminados razonables (excluye `vendor/`, `bin/`, `.env`, `platform/`, herramientas de desarrollo). Sobrescribe en `app.php` solo cuando sea necesario:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor ejecuta un diagnóstico estructurado y sugiere comandos de corrección cuando algo falla:

| Grupo | Comprobaciones |
|-------|--------|
| **Proyecto** | `app.php`, identidad del paquete, estructura de `platform/` |
| **Runtime** | Versión de PHP (≥ 8.1), extensiones, rutas con permiso de escritura |
| **Dependencias** | Vendor de Composer, Node/npm opcional |
| **Entorno** | Presencia de `.env` y variables clave |
| **Base de datos** | Conexión (omitible con `--skip-db`) |
| **Frontend** | Stack del tema, `package.json` (omitible con `--skip-frontend`) |
| **Build** | Preparación de exportación, icono, campos de versión |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # informe apto para CI
pinx doctor --no-fixes      # oculta los comandos sugeridos
```

---

## Referencia de comandos

Ejecuta `pinx list` para un resumen por secciones. Los alias abreviados aparecen entre corchetes.

### Proyecto

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `new` | — | Genera desde `pinoox/app` (asistente o flags) |
| `init` | — | Inicializa el directorio actual (`--force` para sobrescribir) |
| `setup` | — | BD: migra la plataforma + la app, luego ejecuta los seeders |
| `doctor` | `dr` | Comprobación de salud — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | Muestra los metadatos de `app.php` |

### Desarrollo

| Comando | Descripción |
|---------|-------------|
| `dev` | Servidor de desarrollo; Vite cuando `frontend.stack` es vue/react |

### Base de datos

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `migrate:run` | `migrate` | Ejecuta las migraciones de la app (`--platform` ejecuta primero la plataforma) |
| `migrate:status` | `migrate:st` | Estado de las migraciones |
| `migrate:rollback` | `migrate:rb` | Revierte el último lote (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Crea un archivo de migración |
| `migrate:platform` | `migrate:pl` | Solo migraciones de la plataforma |
| `seeder:run` | `seed` | Ejecuta los seeders (`-c` clase) |

### Patches

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `patch:run` | `patch` | Ejecuta los patches pendientes |
| `patch:status` | `patch:st` | Estado de los patches |
| `patch:rollback` | `patch:rb` | Revierte el último lote de patches |

### Build y release

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `build` | `bld` | Construye el paquete `.pinx` |
| `release` | `rel` | Incremento de versión + build (`--bump`, `--sign`) |

### Scaffolding

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Rutas

| Comando | Descripción |
|---------|-------------|
| `route:actions` / `routes` | Lista las named actions (`--validate`, `--json`) |

### Dependencias

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Estado de Composer + npm |
| `deps:install` | `deps:i` | Instala las dependencias |
| `deps:update` | `deps:up` | Actualiza las dependencias |

### Frontend

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | Stack del tema y scripts de npm |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | Build de producción |
| `fe:dev` | `fe:d` | Servidor de desarrollo de Vite |
| `fe:scaffold` | `fe:sc` | Archivos iniciales (`--stack=vue\|react\|twig`) |

### Schedule

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | Lista las tareas cron de `schedule.php` |
| `schedule:run` | `sched:run` | Ejecuta las tareas pendientes (`--dry-run`) |

### Pinion (subidas reanudables)

Reenviado a `php pinoox pinion:*` — gestionar sesiones temporales de subida por fragmentos.

| Comando | Descripción |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

Ver [protocolo Pinion](../advanced/pinion.md).

### Pinker

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Caché vs fuente |
| `pinker:rebuild` | `pinker:rb` | Reconstruye la caché |
| `pinker:diff` | `pinker:df` | Muestra las diferencias |
| `pinker:clear` | `pinker:cl` | Limpia la caché |
| `pinker:overrides` | `pinker:ov` | Lista los overrides |

### Calidad y documentación

| Comando | Descripción |
|---------|-------------|
| `test` / `pest` | Ejecuta las pruebas de la app (`--unit`, `--feature`) |
| `api:docs` | Documentación de la API REST |
| `graphql:docs` | Documentación del esquema GraphQL |

### Meta

| Comando | Alias | Descripción |
|---------|---------|-------------|
| `list` | — | Resumen de comandos agrupados |
| `version` | `ver` | Versión de la CLI |

---

## Detección de la app

Pinx sube desde el directorio de trabajo actual hasta encontrar un proyecto válido de una sola app:

1. `app.php` existe y devuelve un array con una clave `package` no vacía
2. `pinoox/pincore` está requerido en `composer.json`, o `vendor/pinoox/pincore` está presente

Sobrescribe el paquete detectado con variables de entorno:

| Variable | Propósito |
|----------|---------|
| `PINX_PACKAGE` | Fuerza el paquete objetivo de la CLI |
| `PINOOX_DEV_APP` | Alias de `PINX_PACKAGE` |
| `PINX_DEV=1` | Modo de desarrollo (lo establece automáticamente pinx al delegar a pincore) |

---

## Requisitos

- **PHP** ≥ 8.1 con las extensiones requeridas por `pinoox/pincore`
- **Composer** 2.x
- **Node.js** + npm — solo cuando se usan frontends con Vite/Vue/React
- **Base de datos** — MySQL/MariaDB o lo que configure tu `.env` (opcional para apps estáticas o solo Twig)

---

## Documentación relacionada

- [Instalación de Pinoox](./installing-pinoox.md)
- [Referencia de la CLI de Pinoox (multi-app)](./cli-reference.md)
- [Tu primera app](./your-first-app.md)
- [Manifiesto app.php](./app-manifest.md)

---

[← Volver al índice](../README.md)
