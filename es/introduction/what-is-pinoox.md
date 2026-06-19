# ¿Qué es Pinoox?

[← Volver al índice](../README.md)

Pinoox es un framework PHP moderno y de código abierto (3.x) construido sobre la arquitectura HMVC y el concepto de **app**. Hace que el desarrollo web modular sea sencillo: cada app es una unidad MVC independiente bajo `apps/{package}/`, mientras que el núcleo compartido del framework reside en `vendor/pinoox/pincore/`.

---

## Arquitectura centrada en apps

En una sola instalación de Pinoox, varias apps independientes se ejecutan en paralelo:

```
{project_root}/
├── index.php              ← punto de entrada web
├── pinoox                 ← punto de entrada CLI
├── composer.json
├── vendor/pinoox/pincore/ ← núcleo del framework (editar solo para cambios del núcleo)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← tu app
```

- **Proyecto** — la carpeta que contiene `index.php` y `apps/` (el nombre de la carpeta no importa).
- **App** — un módulo completo con sus propios controladores, modelos, rutas, tema y configuración.
- **Núcleo (Core)** — el motor compartido (router, HTTP, base de datos, Twig, CLI y más).

Escribe la lógica de negocio en `apps/`, no en `vendor/pinoox/pincore/`.

---

## Ciclo de vida de la petición HTTP

```
Navegador → index.php → arranque (bootstrap)
       → resolver la app activa (dominio o prefijo de URL)
       → cargar app.php y routes/
       → Flows → Controlador → Modelo (opcional) → Vista o JSON
```

---

## Nomenclatura de apps

Formato de paquete recomendado:

```
com_{vendor}_{name}
```

Ejemplo: `com_acme_shop` — el nombre de la carpeta, el valor `package` en `app.php` y el segmento del namespace deben coincidir.

---

## Ideal para

- Sitios con múltiples secciones y paneles de administración donde cada sección puede ser una app independiente
- Equipos que quieren desarrollar, probar y mantener módulos de forma independiente
- Proyectos PHP 8.2+ con Composer y la CLI integrada (`php pinoox …`)

---

## Documentación relacionada

- [Características de Pinoox](./features-pinoox.md)
- [Instalación de Pinoox](../start/installing-pinoox.md)
- [Tu primera app](../start/your-first-app.md)
- [Tutorial de la API de notas](../examples/simple-api-app.md)
- [Tutorial de la agenda telefónica](../examples/phonebook-app.md)
- [Tutorial del formulario de contacto](../examples/contact-form-app.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)
