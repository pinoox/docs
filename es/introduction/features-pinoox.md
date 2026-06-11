# Características de Pinoox

[← Volver al índice](../README.md)

Pinoox 3.x está diseñado para un ecosistema PHP modular: múltiples apps independientes sobre un núcleo compartido, generación de código por CLI (scaffolding) y herramientas integradas para HTTP, base de datos, temas y autenticación.

---

## Arquitectura HMVC y apps independientes

Cada app bajo `apps/{package}/` tiene una estructura MVC completa:

| Capa | Ruta de ejemplo |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| Vista (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

Agregar o desactivar una app no afecta a las demás.

---

## CLI y desarrollo rápido

Desde la raíz del proyecto:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

La CLI genera la estructura de carpetas estándar, `app.php` y los archivos de rutas iniciales.

---

## Enrutamiento y Named Actions (acciones con nombre)

Las rutas de URL y los manejadores lógicos se mantienen separados:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

Este patrón facilita la refactorización y las pruebas.

---

## Flow (middleware)

Antes de que una petición llegue al controller, se ejecutan los Flows — para autenticación, autorización, registro de logs y más:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Registra los alias de Flow en `app.php`.

---

## Vistas y temas

- Plantillas Twig en `theme/{themeName}/`
- Renderiza con **`View::render()`**
- Soporte para SPA con Vite en el tema (Vue/React)

---

## Base de datos y Eloquent

- Query Builder y Eloquent a través del Portal `DB`
- Migraciones (Migrations) y seeders en `database/migrations/` de cada app
- Prefijo de tabla basado en el nombre del paquete (p. ej. `com_acme_blog_posts`)

---

## API y respuestas JSON

Extiende **`ApiController`** y usa el envoltorio estándar:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## Internacionalización

Archivos de traducción en `lang/{locale}/*.lang.php` — ideales para apps multilingües.

---

## Documentación relacionada

- [¿Qué es Pinoox?](./what-is-pinoox.md)
- [Instalación de Pinoox](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← Volver al índice](../README.md)
