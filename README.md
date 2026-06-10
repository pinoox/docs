# Pinoox Documentation

Official developer documentation for building apps on the Pinoox platform (PHP 8.1+, HMVC architecture).

Each guide describes **one recommended approach** with practical examples. Choose a section below or browse by topic.

**Languages:** [English](./readme.md) · [فارسی](./readme-fa.md)

---

### Introduction

#### [What is Pinoox?](./en/introduction/what-is-pinoox.md)
#### [Features](./en/introduction/features-pinoox.md)
#### [Contributing](./en/introduction/contributions.md)

### Getting Started

#### [Installation](./en/start/installing-pinoox.md)
#### [Your first app](./en/start/your-first-app.md)
#### [Folder structure](./en/start/structure.md)
#### [CLI reference](./en/start/cli-reference.md)
#### [app.php manifest](./en/start/app-manifest.md)

### Practical walkthroughs

#### [Notes API app](./en/examples/simple-api-app.md)
#### [Phonebook web app](./en/examples/phonebook-app.md)
#### [Contact form app](./en/examples/contact-form-app.md)
#### [Simple blog app](./en/examples/blog-app.md)
#### [Task board (Todo)](./en/examples/task-board-app.md)
#### [Image gallery](./en/examples/gallery-app.md)
#### [Vue SPA panel](./en/examples/vue-spa-app.md)
#### [React SPA panel](./en/examples/react-spa-app.md)
#### [Vite hybrid — Twig + JS widget](./en/examples/vite-hybrid-app.md)

### Core Concepts

#### [Routers](./en/basic/routers.md)
#### [Controllers](./en/basic/controllers.md)
#### [Flows (middleware)](./en/basic/flows.md)
#### [Requests](./en/basic/requests.md)
#### [Responses](./en/basic/responses.md)
#### [URL helpers](./en/basic/url.md)
#### [Path helpers](./en/basic/path.md)
#### [Validation](./en/basic/validation.md)
#### [Views](./en/basic/views.md)
#### [Templates (Twig)](./en/basic/templates.md)
#### [Portal (facades)](./en/basic/portal.md)
#### [Config](./en/basic/config.md)
#### [Language / i18n](./en/basic/language.md)

### Advanced Topics

#### [Pinoox Baker (Pinker)](./en/advanced/pinker.md)
#### [Services](./en/advanced/services.md)
#### [Helpers](./en/advanced/helpers.md)
#### [Email](./en/advanced/mail.md)
#### [HTTP Client](./en/advanced/http-client.md)
#### [User management](./en/advanced/user-management.md)
#### [File management](./en/advanced/file-management.md)
#### [Token management](./en/advanced/token-management.md)
#### [Access & permissions](./en/advanced/access-permissions.md)
#### [boot.php & events](./en/advanced/boot-and-events.md)
#### [Schedule / cron](./en/advanced/schedule.md)

### Database

#### [Getting started](./en/database/getting-started.md)
#### [Query Builder](./en/database/query-builder.md)
#### [Pagination](./en/database/pagination.md)
#### [Migrations](./en/database/migrations.md)
#### [Patches (data updates)](./en/database/patches.md)

### Eloquent ORM

#### [Getting started](./en/eloquent-orm/getting-started.md)
#### [Relationships](./en/eloquent-orm/relationships.md)
#### [Collections](./en/eloquent-orm/collections.md)
#### [Mutators & casts](./en/eloquent-orm/mutators-casts.md)
#### [API resources](./en/eloquent-orm/api-resources.md)
#### [Serialization](./en/eloquent-orm/serialization.md)
#### [Factories](./en/eloquent-orm/factories.md)

### Testing

#### [Getting started](./en/test/getting-started.md)
#### [HTTP tests](./en/test/http-tests.md)
#### [Console tests](./en/test/console-tests.md)
#### [Browser tests](./en/test/browser-tests.md)
#### [Database](./en/test/database.md)
#### [Serialization](./en/test/serialization.md)
#### [Mocking](./en/test/mocking.md)

### FAQ

#### [Common issues](./en/faq/common-issues.md)
#### [Contact support](./en/faq/contact-support.md)

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
