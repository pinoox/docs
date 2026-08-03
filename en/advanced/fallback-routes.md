# Fallback Routes

[← Back to index](../README.md)

When no normal route matches, Pinoox can run a **fallback** action for that URL scope. Fallbacks reuse the catch-all `*` matcher with **prefix-aware priority** so the nearest group wins.

> Use **`Route::fallback()`** or helper **`fallback()`**. Without a fallback, the framework’s default 404 / no-route response still applies.

---

## Overview

| Scope | How |
|-------|-----|
| Global | `Route::fallback(...)` at the root of a route file |
| Group / prefix | `fallback()` inside `group()` or `prefix()` |
| API / web / module | Separate fallbacks per collection or route file |
| Flow | `->flow([...])` on the fallback builder |

Matching order:

1. Normal routes  
2. **Nearest** fallback (longer prefix → higher catch-all priority)  
3. Framework default 404 if nothing is registered  

---

## Global fallback

```php
use Pinoox\Portal\View;
use function Pinoox\Router\fallback;

fallback(fn () => View::render('errors/404'))->name('fallback');

// or
use Pinoox\Portal\Route;

Route::fallback(fn () => view('404'));
Route::fallback(NotFoundController::class);
```

---

## Group / API fallback

```php
use function Pinoox\Router\{get, group, fallback};

group(['prefix' => '/api', 'flows' => ['cors:api']], function () {
    get('/products', [ProductController::class, 'index']);

    fallback(function () {
        return response()->json(['message' => 'Not Found'], 404);
    })->name('fallback.api');
});
```

`Route::group('/api', function () { ... })` also works (string seed = prefix).

---

## Prefix helper

```php
use Pinoox\Portal\Route;

Route::prefix('/admin', function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard']);

    Route::fallback(Admin404Controller::class)->name('fallback.admin');
});
```

---

## Flow integration

Fallbacks are real routes — Flows run as usual:

```php
Route::fallback(function () {
    return response()->json(['message' => 'Not Found'], 404);
})->flow([
    'cors:api',
])->name('fallback.json');
```

---

## Priority example

```php
Route::fallback(fn () => 'global')->name('fallback.global');

Route::group(['prefix' => '/api'], function () {
    Route::fallback(fn () => 'api')->name('fallback.api');
});
```

| Request | Winner |
|---------|--------|
| `/api/missing` | `fallback.api` |
| `/other` | `fallback.global` |

---

## Best practices

1. Return **404** (or JSON 404) from API fallbacks — matching alone does not set the status.  
2. Name fallbacks (`fallback`, `fallback.api`) for debugging.  
3. Put API fallbacks inside the `/api` group so they don’t catch web URLs.  
4. Keep SPA shells as a dedicated fallback (see manager `get('*', …)` / `fallback()`).  
5. Prefer one fallback per scope; nested modules can each register their own.

---

## Related docs

- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)
- [Responses](../basic/responses.md)

---

[← Back to index](../README.md)
