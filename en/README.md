# Pinoox Documentation

Official developer documentation for building apps on the Pinoox platform (PHP 8.2+, HMVC architecture).

Each guide describes **one recommended approach** with practical examples. Choose a section below or browse by topic.

**Languages:** [English](./README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](../de/README.md)

---

### Introduction

#### [What is Pinoox?](./introduction/what-is-pinoox.md)
#### [Pinoox features](./introduction/features-pinoox.md)
#### [Contributing to Pinoox](./introduction/contributions.md)

### Getting Started

#### [Installing Pinoox](./start/installing-pinoox.md)
#### [Your first app](./start/your-first-app.md)
#### [Project structure](./start/structure.md)
#### [Pinoox CLI reference](./start/cli-reference.md)
#### [Pinx CLI (single-app projects)](./start/pinx-cli.md)
#### [app.php manifest reference](./start/app-manifest.md)

### Practical walkthroughs

#### [Walkthrough: Notes API app](./examples/simple-api-app.md)
#### [Walkthrough: Phonebook web app](./examples/phonebook-app.md)
#### [Walkthrough: Contact form app](./examples/contact-form-app.md)
#### [Walkthrough: Simple blog app](./examples/blog-app.md)
#### [Walkthrough: Task board (Todo)](./examples/task-board-app.md)
#### [Walkthrough: Image gallery app](./examples/gallery-app.md)
#### [Walkthrough: Vue SPA panel](./examples/vue-spa-app.md)
#### [Walkthrough: React SPA panel](./examples/react-spa-app.md)
#### [Walkthrough: Vite hybrid (Twig + JS widget)](./examples/vite-hybrid-app.md)

### Core Concepts

#### [Router](./basic/routers.md)
#### [Controllers](./basic/controllers.md)
#### [Flow (middleware)](./basic/flows.md)
#### [HTTP Request](./basic/requests.md)
#### [HTTP Response](./basic/responses.md)
#### [URL and Link Building](./basic/url.md)
#### [File Path](./basic/path.md)
#### [Validation](./basic/validation.md)
#### [Views](./basic/views.md)
#### [Twig Templates](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [Language and Translation](./basic/language.md)
#### [Date and calendar](./basic/date-and-calendar.md)

### Advanced Topics

#### [Pinker and Cache](./advanced/pinker.md)
#### [App Services (Component + Portal)](./advanced/services.md)
#### [Global Helpers](./advanced/helpers.md)
#### [Sending Email](./advanced/mail.md)
#### [HTTP Client](./advanced/http-client.md)
#### [User Management](./advanced/user-management.md)
#### [File Management](./advanced/file-management.md)
#### [Pinion Protocol](./advanced/pinion.md)
#### [Token Management](./advanced/token-management.md)
#### [Access & permissions](./advanced/access-permissions.md)
#### [Transport (shared resources)](./advanced/transport.md)
#### [boot.php and events](./advanced/boot-and-events.md)
#### [Scheduling (cron)](./advanced/schedule.md)

### Database

#### [Database Getting Started](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Pagination](./database/pagination.md)
#### [Migrations](./database/migrations.md)
#### [Patches (data updates)](./database/patches.md)

### Eloquent ORM

#### [Eloquent ORM Getting Started](./eloquent-orm/getting-started.md)
#### [Eloquent Relationships](./eloquent-orm/relationships.md)
#### [Eloquent Collections](./eloquent-orm/collections.md)
#### [Mutators and Casts](./eloquent-orm/mutators-casts.md)
#### [API Resources](./eloquent-orm/api-resources.md)
#### [Model Serialization](./eloquent-orm/serialization.md)
#### [Test Data — Seeders](./eloquent-orm/factories.md)

### Testing

#### [Getting Started with Testing in Pinoox](./test/getting-started.md)
#### [HTTP Testing in Pinoox](./test/http-tests.md)
#### [Console Testing in Pinoox](./test/console-tests.md)
#### [Browser (HTML) Testing in Pinoox](./test/browser-tests.md)
#### [Database Testing in Pinoox](./test/database.md)
#### [Serialization Testing in Pinoox](./test/serialization.md)
#### [Mocking in Pinoox](./test/mocking.md)

### FAQ

#### [Common Issues](./faq/common-issues.md)
#### [Contact Support](./faq/contact-support.md)

---

### Source
**Example source:** [docs/source/](../source/) — full code for every walkthrough

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
