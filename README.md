# Pinoox Documentation

Official developer documentation for building and shipping Pinoox apps.

The recommended path is **Pinx single-app development**: one project, one app, one local workflow, then a `.pinx` package for release.

**Languages:** [English](./en/README.md) | [فارسی](./fa/README.md) | [العربية](./ar/README.md) | [中文](./zh/README.md) | [日本語](./ja/README.md) | [한국어](./ko/README.md) | [Türkçe](./tr/README.md) | [Español](./es/README.md) | [हिन्दी](./hi/README.md) | [Français](./fr/README.md) | [Русский](./ru/README.md) | [Português](./pt/README.md) | [Deutsch](./de/README.md)

---

## Start Here: Build A Pinx App

Read these in order if you are building a new app.

1. [Install Pinoox and Pinx](./en/start/installing-pinoox.md)
2. [Create your first single-app project](./en/start/your-first-app.md)
3. [Understand the single-app structure](./en/start/structure.md)
4. [Use DevDB for local development](./en/start/devdb.md)
5. [Use Pinx Inspector while developing](./en/start/pinx-cli.md#pinx-inspector)
6. [Add routes, controllers, views, models, and migrations](./en/start/your-first-app.md#build-the-first-feature)
7. [Test your app](./en/test/getting-started.md)
8. [Build and release a `.pinx` package](./en/start/build-release.md)

## Daily Pinx Workflow

- [Pinx CLI guide and command reference](./en/start/pinx-cli.md)
- [app.php manifest reference](./en/start/app-manifest.md)
- [App dependencies](./en/start/app-depends.md)
- [Dependencies CLI (`deps`)](./en/start/deps-cli.md)
- [Package naming rules](./en/start/package-naming.md)
- [Pinoox CLI reference for platform installs](./en/start/cli-reference.md)

## Core App Development

- [Router](./en/basic/routers.md)
- [Controllers](./en/basic/controllers.md)
- [Flow middleware](./en/basic/flows.md)
- [HTTP Request](./en/basic/requests.md)
- [HTTP Response](./en/basic/responses.md)
- [URL and link building](./en/basic/url.md)
- [Validation](./en/basic/validation.md)
- [Views](./en/basic/views.md)
- [Twig templates](./en/basic/templates.md)
- [Theme contexts](./en/basic/theme-contexts.md)
- [Theme manifest (`theme.php`)](./en/basic/theme-manifest.md)
- [Frontend & Vite](./en/basic/frontend-vite.md)
- [@pinooxhq/vite-plugin](./en/basic/vite-plugin.md)
- [Config](./en/basic/config.md)
- [Language and translation](./en/basic/language.md)
- [Date and calendar](./en/basic/date-and-calendar.md)

## Database And Models

- [Database getting started](./en/database/getting-started.md)
- [Migrations](./en/database/migrations.md)
- [Query Builder](./en/database/query-builder.md)
- [Pagination](./en/database/pagination.md)

- [Eloquent ORM getting started](./en/eloquent-orm/getting-started.md)
- [Eloquent relationships](./en/eloquent-orm/relationships.md)
- [Factories and seeders](./en/eloquent-orm/factories.md)
- [Mutators and casts](./en/eloquent-orm/mutators-casts.md)
- [API resources](./en/eloquent-orm/api-resources.md)
- [Serialization](./en/eloquent-orm/serialization.md)

## Build, Runtime, And Advanced Features

- [Pinker and cache](./en/advanced/pinker.md)
- [Patches for data updates](./en/advanced/patches.md)
- [Scheduling](./en/advanced/schedule.md)
- [App services](./en/advanced/services.md)
- [Global helpers](./en/advanced/helpers.md)
- [Mail](./en/advanced/mail.md)
- [HTTP client](./en/advanced/http-client.md)
- [User management](./en/advanced/user-management.md)
- [File management](./en/advanced/file-management.md)
- [Pinion uploads](./en/advanced/pinion.md)
- [Token management](./en/advanced/token-management.md)
- [Access and permissions](./en/advanced/access-permissions.md)
- [Transport and shared resources](./en/advanced/transport.md)
- [Kernel and boot pipeline](./en/advanced/kernel.md)
- [boot.php and events](./en/advanced/boot-and-events.md)

## Walkthroughs

Use these after the start guide when you want concrete examples.

- [Notes API app](./en/examples/simple-api-app.md)
- [Phonebook web app](./en/examples/phonebook-app.md)
- [Contact form app](./en/examples/contact-form-app.md)
- [Simple blog app](./en/examples/blog-app.md)
- [Task board app](./en/examples/task-board-app.md)
- [Image gallery app](./en/examples/gallery-app.md)
- [Vue SPA panel](./en/examples/vue-spa-app.md)
- [React SPA panel](./en/examples/react-spa-app.md)
- [Vite hybrid app](./en/examples/vite-hybrid-app.md)

## Testing

- [Testing overview](./en/test/getting-started.md)
- [HTTP tests](./en/test/http-tests.md)
- [Console tests](./en/test/console-tests.md)
- [Browser tests](./en/test/browser-tests.md)
- [Database tests](./en/test/database.md)
- [Serialization tests](./en/test/serialization.md)
- [Mocking](./en/test/mocking.md)

## Platform And Background

Most app developers can start with Pinx. Use these when you maintain a full Pinoox platform or the framework itself.

- [What is Pinoox?](./en/introduction/what-is-pinoox.md)
- [Pinoox features](./en/introduction/features-pinoox.md)
- [Contributing](./en/introduction/contributions.md)
- [Common issues](./en/faq/common-issues.md)
- [Contact support](./en/faq/contact-support.md)

---

## How To Read These Docs

1. Follow **Start Here** from top to bottom.
2. Keep **Pinx CLI** open while developing.
3. Use **Core App Development** when you add app features.
4. Use **Database And Models** when a feature needs persistence.
5. Run `pinx doctor`, `pinx test`, then `pinx build` before release.

For Pinx projects, app code lives at the project root. Do not put app business logic inside `vendor/pinoox/pincore`.
