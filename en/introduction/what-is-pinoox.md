# What is Pinoox?

[← Back to index](../../README.md)

Pinoox is a modern, open-source PHP framework (3.x) built on HMVC architecture and the **app** concept. It makes modular web development straightforward: each app is an independent MVC unit under `apps/{package}/`, while the shared framework core lives in `vendor/pinoox/pincore/`.

---

## App-centric architecture

In a single Pinoox installation, multiple independent apps run side by side:

```
{project_root}/
├── index.php              ← web entry point
├── pinoox                 ← CLI entry point
├── composer.json
├── vendor/pinoox/pincore/ ← framework core (edit only for core changes)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← your app
```

- **Project** — the folder that contains `index.php` and `apps/` (the folder name does not matter).
- **App** — a complete module with its own controllers, models, routes, theme, and config.
- **Core** — the shared engine (router, HTTP, database, Twig, CLI, and more).

Write business logic in `apps/`, not in `vendor/pinoox/pincore/`.

---

## HTTP request lifecycle

```
Browser → index.php → bootstrap
       → resolve active app (domain or URL prefix)
       → load app.php and routes/
       → Flows → Controller → Model (optional) → View or JSON
```

---

## App naming

Recommended package format:

```
com_{vendor}_{name}
```

Example: `com_acme_shop` — the folder name, the `package` value in `app.php`, and the namespace segment must all match.

---

## Good fit for

- Multi-section sites and admin panels where each section can be a separate app
- Teams that want to develop, test, and maintain modules independently
- PHP 8.1+ projects with Composer and the integrated CLI (`php pinoox …`)

---

## Related docs

- [Pinoox features](./features-pinoox.md)
- [Installing Pinoox](../start/installing-pinoox.md)
- [Your first app](../start/your-first-app.md)
- [Notes API walkthrough](../examples/simple-api-app.md)
- [Phonebook walkthrough](../examples/phonebook-app.md)
- [Contact form walkthrough](../examples/contact-form-app.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../../README.md)
