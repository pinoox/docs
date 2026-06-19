# Pinoox Documentation

Official developer documentation for building apps on the Pinoox platform (PHP 8.2+, HMVC architecture).

Each guide describes **one recommended approach** with practical examples. Choose a section below or browse by topic.

**Languages:** [English](./README.md) · [فارسی](./fa/README.md) · [العربية](./ar/README.md) · [中文](./zh/README.md) · [日本語](./ja/README.md) · [한국어](./ko/README.md) · [Türkçe](./tr/README.md) · [Español](./es/README.md) · [हिन्दी](./hi/README.md) · [Français](./fr/README.md) · [Русский](./ru/README.md) · [Português](./pt/README.md) · [Deutsch](./de/README.md)

---

### Introduction

#### [What is Pinoox?](./en/introduction/what-is-pinoox.md)
#### [Pinoox features](./en/introduction/features-pinoox.md)
#### [Contributing to Pinoox](./en/introduction/contributions.md)

### Getting Started

#### [Installing Pinoox](./en/start/installing-pinoox.md)
#### [Your first app](./en/start/your-first-app.md)
#### [Project structure](./en/start/structure.md)
#### [Pinoox CLI reference](./en/start/cli-reference.md)
#### [Pinx CLI (single-app projects)](./en/start/pinx-cli.md)
#### [app.php manifest reference](./en/start/app-manifest.md)

### Practical walkthroughs

#### [Walkthrough: Notes API app](./en/examples/simple-api-app.md)
#### [Walkthrough: Phonebook web app](./en/examples/phonebook-app.md)
#### [Walkthrough: Contact form app](./en/examples/contact-form-app.md)
#### [Walkthrough: Simple blog app](./en/examples/blog-app.md)
#### [Walkthrough: Task board (Todo)](./en/examples/task-board-app.md)
#### [Walkthrough: Image gallery app](./en/examples/gallery-app.md)
#### [Walkthrough: Vue SPA panel](./en/examples/vue-spa-app.md)
#### [Walkthrough: React SPA panel](./en/examples/react-spa-app.md)
#### [Walkthrough: Vite hybrid (Twig + JS widget)](./en/examples/vite-hybrid-app.md)

### Core Concepts

#### [Router](./en/basic/routers.md)
#### [Controllers](./en/basic/controllers.md)
#### [Flow (middleware)](./en/basic/flows.md)
#### [HTTP Request](./en/basic/requests.md)
#### [HTTP Response](./en/basic/responses.md)
#### [URL and Link Building](./en/basic/url.md)
#### [File Path](./en/basic/path.md)
#### [Validation](./en/basic/validation.md)
#### [Views](./en/basic/views.md)
#### [Twig Templates](./en/basic/templates.md)
#### [Portal (Facade)](./en/basic/portal.md)
#### [Config](./en/basic/config.md)
#### [Language and Translation](./en/basic/language.md)
#### [Date and calendar](./en/basic/date-and-calendar.md)

### Advanced Topics

#### [Pinker and Cache](./en/advanced/pinker.md)
#### [App Services (Component + Portal)](./en/advanced/services.md)
#### [Global Helpers](./en/advanced/helpers.md)
#### [Sending Email](./en/advanced/mail.md)
#### [HTTP Client](./en/advanced/http-client.md)
#### [User Management](./en/advanced/user-management.md)
#### [File Management](./en/advanced/file-management.md)
#### [Pinion Protocol](./en/advanced/pinion.md)
#### [Token Management](./en/advanced/token-management.md)
#### [Access & permissions](./en/advanced/access-permissions.md)
#### [Transport (shared resources)](./en/advanced/transport.md)
#### [boot.php and events](./en/advanced/boot-and-events.md)
#### [Scheduling (cron)](./en/advanced/schedule.md)

### Database

#### [Database Getting Started](./en/database/getting-started.md)
#### [Query Builder](./en/database/query-builder.md)
#### [Pagination](./en/database/pagination.md)
#### [Migrations](./en/database/migrations.md)
#### [Patches (data updates)](./en/database/patches.md)

### Eloquent ORM

#### [Eloquent ORM Getting Started](./en/eloquent-orm/getting-started.md)
#### [Eloquent Relationships](./en/eloquent-orm/relationships.md)
#### [Eloquent Collections](./en/eloquent-orm/collections.md)
#### [Mutators and Casts](./en/eloquent-orm/mutators-casts.md)
#### [API Resources](./en/eloquent-orm/api-resources.md)
#### [Model Serialization](./en/eloquent-orm/serialization.md)
#### [Test Data — Seeders](./en/eloquent-orm/factories.md)

### Testing

#### [Getting Started with Testing in Pinoox](./en/test/getting-started.md)
#### [HTTP Testing in Pinoox](./en/test/http-tests.md)
#### [Console Testing in Pinoox](./en/test/console-tests.md)
#### [Browser (HTML) Testing in Pinoox](./en/test/browser-tests.md)
#### [Database Testing in Pinoox](./en/test/database.md)
#### [Serialization Testing in Pinoox](./en/test/serialization.md)
#### [Mocking in Pinoox](./en/test/mocking.md)

### FAQ

#### [Common Issues](./en/faq/common-issues.md)
#### [Contact Support](./en/faq/contact-support.md)

---

### Source
**Example source:** [docs/source/](./source/) — full code for every walkthrough

Step-by-step guides for real apps — use these after reading the basics and when you want hands-on code.

---

### How to read these docs

1. Start with **Introduction** and **Getting Started** if you are new to Pinoox.
2. Follow **Practical walkthroughs** — build a JSON API and a simple website step by step.
3. Read **Core Concepts** while building routes, controllers, and views.
4. Use **Database** and **Eloquent ORM** when you add persistence.
5. Refer to **Advanced Topics** for auth, files, Pinker, and shared services.
6. Use **Testing** before shipping features to production.

All app code lives under `apps/{package}/`. The framework core is `vendor/pinoox/pincore/` — do not put app business logic there.
