# Pinoox Documentation

Official developer documentation for building and shipping Pinoox apps.

The recommended path is **Pinx single-app development**: one project, one app, one local workflow, then a `.pinx` package for release.

**Languages:** [English](./README.md) | [فارسی](../fa/README.md) | [العربية](../ar/README.md) | [中文](../zh/README.md) | [日本語](../ja/README.md) | [한국어](../ko/README.md) | [Türkçe](../tr/README.md) | [Español](../es/README.md) | [हिन्दी](../hi/README.md) | [Français](../fr/README.md) | [Русский](../ru/README.md) | [Português](../pt/README.md) | [Deutsch](../de/README.md)

---

## Start Here: Build A Pinx App

Read these in order if you are building a new app.

1. [Install Pinoox and Pinx](./start/installing-pinoox.md)
2. [Create your first single-app project](./start/your-first-app.md)
3. [Understand the single-app structure](./start/structure.md)
4. [Use DevDB for local development](./start/devdb.md)
5. [Use Pinx Inspector while developing](./start/pinx-cli.md#pinx-inspector)
6. [Add routes, controllers, views, models, and migrations](./start/your-first-app.md#build-the-first-feature)
7. [Test your app](./test/getting-started.md)
8. [Build and release a `.pinx` package](./start/build-release.md)

## Daily Pinx Workflow

- [Pinx CLI guide and command reference](./start/pinx-cli.md)
- [app.php manifest reference](./start/app-manifest.md)
- [Package naming rules](./start/package-naming.md)
- [Pinoox CLI reference for platform installs](./start/cli-reference.md)

## Core App Development

- [Router](./basic/routers.md)
- [Controllers](./basic/controllers.md)
- [Flow middleware](./basic/flows.md)
- [HTTP Request](./basic/requests.md)
- [HTTP Response](./basic/responses.md)
- [URL and link building](./basic/url.md)
- [Validation](./basic/validation.md)
- [Views](./basic/views.md)
- [Frontend & Vite](./basic/frontend-vite.md)
- [@pinooxhq/vite-plugin](./basic/vite-plugin.md)
- [Twig templates](./basic/templates.md)
- [Config](./basic/config.md)
- [Language and translation](./basic/language.md)
- [Date and calendar](./basic/date-and-calendar.md)

## Database And Models

- [Database getting started](./database/getting-started.md)
- [Migrations](./database/migrations.md)
- [Query Builder](./database/query-builder.md)
- [Pagination](./database/pagination.md)
- [Patches for data updates](./database/patches.md)
- [Eloquent ORM getting started](./eloquent-orm/getting-started.md)
- [Eloquent relationships](./eloquent-orm/relationships.md)
- [Factories and seeders](./eloquent-orm/factories.md)
- [Mutators and casts](./eloquent-orm/mutators-casts.md)
- [API resources](./eloquent-orm/api-resources.md)
- [Serialization](./eloquent-orm/serialization.md)

## Deploy

Platform release and rollout (full Pinoox installations).

- [Pinroll — release & deploy](./deploy/pinroll.md)

## Build, Runtime, And Advanced Features

- [Pinker and cache](./advanced/pinker.md)
- [Scheduling](./advanced/schedule.md)
- [App services](./advanced/services.md)
- [Global helpers](./advanced/helpers.md)
- [Mail](./advanced/mail.md)
- [HTTP client](./advanced/http-client.md)
- [User management](./advanced/user-management.md)
- [File management](./advanced/file-management.md)
- [Pinion uploads](./advanced/pinion.md)
- [Token management](./advanced/token-management.md)
- [Access and permissions](./advanced/access-permissions.md)
- [Transport and shared resources](./advanced/transport.md)
- [boot.php and events](./advanced/boot-and-events.md)

## Walkthroughs

Use these after the start guide when you want concrete examples.

- [Notes API app](./examples/simple-api-app.md)
- [Phonebook web app](./examples/phonebook-app.md)
- [Contact form app](./examples/contact-form-app.md)
- [Simple blog app](./examples/blog-app.md)
- [Task board app](./examples/task-board-app.md)
- [Image gallery app](./examples/gallery-app.md)
- [Vue SPA panel](./examples/vue-spa-app.md)
- [React SPA panel](./examples/react-spa-app.md)
- [Vite hybrid app](./examples/vite-hybrid-app.md)

## Testing

- [Testing overview](./test/getting-started.md)
- [HTTP tests](./test/http-tests.md)
- [Console tests](./test/console-tests.md)
- [Browser tests](./test/browser-tests.md)
- [Database tests](./test/database.md)
- [Serialization tests](./test/serialization.md)
- [Mocking](./test/mocking.md)

## Platform And Background

Most app developers can start with Pinx. Use these when you maintain a full Pinoox platform or the framework itself.

- [What is Pinoox?](./introduction/what-is-pinoox.md)
- [Pinoox features](./introduction/features-pinoox.md)
- [Contributing](./introduction/contributions.md)
- [Common issues](./faq/common-issues.md)
- [Contact support](./faq/contact-support.md)

---

## How To Read These Docs

1. Follow **Start Here** from top to bottom.
2. Keep **Pinx CLI** open while developing.
3. Use **Core App Development** when you add app features.
4. Use **Database And Models** when a feature needs persistence.
5. Run `pinx doctor`, `pinx test`, then `pinx build` before release.

For Pinx projects, app code lives at the project root. Do not put app business logic inside `vendor/pinoox/pincore`.
