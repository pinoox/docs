# Documentación de Pinoox

Documentación oficial para desarrollar aplicaciones en la plataforma Pinoox (PHP 8.2+, arquitectura HMVC).

Cada guía describe **un enfoque recomendado** con ejemplos prácticos. Elige una sección o explora por tema.

**Idiomas:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](./README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### Introducción

#### [¿Qué es Pinoox?](./introduction/what-is-pinoox.md)
#### [Características de Pinoox](./introduction/features-pinoox.md)
#### [Contribuir a Pinoox](./introduction/contributions.md)

### Primeros pasos

#### [Instalación de Pinoox](./start/installing-pinoox.md)
#### [Tu primera app](./start/your-first-app.md)
#### [Estructura del proyecto](./start/structure.md)
#### [Referencia de la CLI de Pinoox](./start/cli-reference.md)
#### [Pinx CLI (proyectos de una sola app)](./start/pinx-cli.md)
#### [Referencia del manifiesto app.php](./start/app-manifest.md)

### Guías prácticas

#### [Tutorial: aplicación de API de notas](./examples/simple-api-app.md)
#### [Tutorial: aplicación web de agenda telefónica](./examples/phonebook-app.md)
#### [Tutorial: aplicación de formulario de contacto](./examples/contact-form-app.md)
#### [Tutorial: aplicación de blog simple](./examples/blog-app.md)
#### [Tutorial: tablero de tareas (Todo)](./examples/task-board-app.md)
#### [Tutorial: aplicación de galería de imágenes](./examples/gallery-app.md)
#### [Tutorial: panel SPA con Vue](./examples/vue-spa-app.md)
#### [Tutorial: panel SPA con React](./examples/react-spa-app.md)
#### [Tutorial: híbrido Vite (Twig + widget JS)](./examples/vite-hybrid-app.md)

### Conceptos básicos

#### [Router](./basic/routers.md)
#### [Controllers](./basic/controllers.md)
#### [Flow (middleware)](./basic/flows.md)
#### [Request HTTP](./basic/requests.md)
#### [Response HTTP](./basic/responses.md)
#### [URL y construcción de enlaces](./basic/url.md)
#### [Rutas de archivos (Path)](./basic/path.md)
#### [Validación](./basic/validation.md)
#### [Vistas](./basic/views.md)
#### [Plantillas Twig](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [Idioma y traducción](./basic/language.md)

### Temas avanzados

#### [Pinker y caché](./advanced/pinker.md)
#### [Servicios de app (Component + Portal)](./advanced/services.md)
#### [Helpers globales](./advanced/helpers.md)
#### [Envío de correo](./advanced/mail.md)
#### [Cliente HTTP](./advanced/http-client.md)
#### [Gestión de usuarios](./advanced/user-management.md)
#### [Gestión de archivos](./advanced/file-management.md)
#### [Protocolo Pinion](./advanced/pinion.md)
#### [Gestión de tokens](./advanced/token-management.md)
#### [Acceso y permisos](./advanced/access-permissions.md)
#### [Transport (recursos compartidos)](./advanced/transport.md)
#### [boot.php y eventos](./advanced/boot-and-events.md)
#### [Programación (cron)](./advanced/schedule.md)

### Base de datos

#### [Primeros pasos con la base de datos](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Paginación](./database/pagination.md)
#### [Migraciones](./database/migrations.md)
#### [Patches (actualizaciones de datos)](./database/patches.md)

### Eloquent ORM

#### [Primeros pasos con Eloquent ORM](./eloquent-orm/getting-started.md)
#### [Relaciones Eloquent](./eloquent-orm/relationships.md)
#### [Colecciones Eloquent](./eloquent-orm/collections.md)
#### [Mutators y casts](./eloquent-orm/mutators-casts.md)
#### [API Resources](./eloquent-orm/api-resources.md)
#### [Serialización de modelos](./eloquent-orm/serialization.md)
#### [Datos de prueba — Seeders](./eloquent-orm/factories.md)

### Pruebas

#### [Primeros pasos con testing en Pinoox](./test/getting-started.md)
#### [Tests HTTP en Pinoox](./test/http-tests.md)
#### [Tests de consola en Pinoox](./test/console-tests.md)
#### [Tests de navegador (HTML) en Pinoox](./test/browser-tests.md)
#### [Tests de base de datos en Pinoox](./test/database.md)
#### [Tests de serialización en Pinoox](./test/serialization.md)
#### [Mocking en Pinoox](./test/mocking.md)

### Preguntas frecuentes

#### [Problemas frecuentes](./faq/common-issues.md)
#### [Contactar soporte](./faq/contact-support.md)

---

### Código fuente
**Código de ejemplo:** [docs/source/](../source/) — código completo de cada guía

Guías paso a paso para apps reales — úsalas después de leer lo básico cuando quieras código práctico.

---

### Cómo leer esta documentación

1. Empieza con **Introducción** y **Primeros pasos** si eres nuevo en Pinoox.
2. Sigue las **Guías prácticas** — construye una API JSON y un sitio simple paso a paso.
3. Lee los **Conceptos básicos** mientras creas rutas, controladores y vistas.
4. Usa **Base de datos** y **Eloquent ORM** al añadir persistencia.
5. Consulta **Temas avanzados** para auth, archivos, Pinker y servicios compartidos.
6. Usa **Pruebas** antes de llevar features a producción.

Todo el código de apps vive en `apps/{package}/`. El núcleo del framework es `vendor/pinoox/pincore/` — no pongas lógica de negocio allí.
