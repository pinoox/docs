# Pinoox features

[← Back to index](../../readme.md)

Pinoox 3.x is designed for a modular PHP ecosystem: multiple independent apps on one shared core, CLI scaffolding, and built-in tools for HTTP, database, themes, and authentication.

---

## HMVC architecture and independent apps

Each app under `apps/{package}/` has a complete MVC structure:

| Layer | Example path |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

Adding or disabling one app does not affect the others.

---

## CLI and rapid development

From the project root:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

The CLI generates the standard folder layout, `app.php`, and initial route files.

---

## Routing and Named Actions

URL paths and logical handlers are kept separate:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

This pattern makes refactoring and testing easier.

---

## Flow (middleware)

Before a request reaches the controller, Flows run — for authentication, authorization, logging, and more:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Register Flow aliases in `app.php`.

---

## Views and themes

- Twig templates in `theme/{themeName}/`
- Render with **`View::render()`**
- SPA support with Vite in the theme (Vue/React)

---

## Database and Eloquent

- Query Builder and Eloquent via the `DB` Portal
- Migrations and seeders in each app's `database/migrations/`
- Table prefix based on package name (e.g. `com_acme_blog_posts`)

---

## API and JSON responses

Extend **`ApiController`** and use the standard envelope:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## Internationalization

Translation files in `lang/{locale}/*.lang.php` — suitable for multilingual apps.

---

## Related docs

- [What is Pinoox?](./what-is-pinoox.md)
- [Installing Pinoox](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← Back to index](../../readme.md)
