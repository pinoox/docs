# Route Parameters

[← Back to index](../README.md)

Pinoox routes already support `{id}` placeholders. This page covers the **expressive parameter syntax** built on top of that system: optional segments, catch-alls, built-in types, enums, file extensions, and reusable patterns — without rewriting the router.

> Paths are compiled **once** at registration into Symfony-compatible requirements and defaults. Invalid typed or enum values simply **do not match** → framework 404, controller never runs.

---

## Quick examples

```php
use Pinoox\Portal\Route;
use function Pinoox\Router\{get, pattern};

get('/users/{id?}', [UserController::class, 'show']);
get('/docs/{path*}', [DocsController::class, 'page']);
get('/items/{id:int}', [ItemController::class, 'show']);
get('/orders/{status:pending|paid|cancelled}', [OrderController::class, 'byStatus']);
get('/files/{name}.{ext}', [FileController::class, 'show']);
get('/download/{app}/{path*}', [DownloadController::class, 'file']);

pattern('username', '[a-z][a-z0-9_]{2,20}');
get('/u/{username:username}', [ProfileController::class, 'show']);
```

Access resolved values:

```php
$request->route('id');
$request->route('path');
$request->route('status', 'pending'); // with default
$request->route(); // current Route object
```

---

## Optional parameters

```text
/users/{id?}
```

| URL | `id` |
|-----|------|
| `/users` | `null` |
| `/users/15` | `"15"` |

Typed optional form:

```php
get('/users/{id?:int}', ...);
```

---

## Catch-all parameters

```text
/docs/{path*}
```

| URL | `path` |
|-----|--------|
| `/docs` | `""` (empty string) |
| `/docs/install` | `"install"` |
| `/docs/install/php/linux` | `"install/php/linux"` |

The value is the remaining path **without** a leading slash.

---

## Optional catch-all

```text
/docs/{path*?}
```

Same matches as `{path*}`, but when the segment is omitted:

| URL | `path` |
|-----|--------|
| `/docs` | `null` |
| `/docs/install/php` | `"install/php"` |

---

## Built-in types

Use `{name:type}` — no regex required for common cases.

| Type | Matches (summary) |
|------|-------------------|
| `int` | Digits only |
| `number` | Integer or decimal |
| `uuid` | UUID string |
| `ulid` | 26-char ULID |
| `slug` | `my-post-title` |
| `alpha` | Letters only |
| `alnum` | Letters + digits |
| `hex` | Hex digits |
| `email` | Simple email shape |
| `domain` | Hostname-like domain |
| `ip` | IPv4 / IPv6 |
| `url` | `http://` or `https://` URL |

```php
get('/items/{id:int}', ...);
get('/posts/{slug:slug}', ...);
get('/accounts/{id:uuid}', ...);
```

A non-matching value (e.g. `/items/abc` for `{id:int}`) yields **404**.

---

## Enum values

Pipe-separated literals:

```php
get('/orders/{status:pending|paid|cancelled}', [OrderController::class, 'byStatus']);
```

| URL | Result |
|-----|--------|
| `/orders/paid` | `status = "paid"` |
| `/orders/refunded` | 404 |

---

## File extensions

Literal dots between parameters are supported:

```php
get('/files/{name}.{ext}', [FileController::class, 'show']);
// /files/logo.png  →  name=logo, ext=png
```

Combine with types when useful:

```php
get('/assets/{name}.{ext:alpha}', ...);
```

---

## Multiple parameters

```php
get('/download/{app}/{path*}', [DownloadController::class, 'file']);
// /download/pinoox/releases/v3/file.zip
//   app  = pinoox
//   path = releases/v3/file.zip
```

---

## Custom patterns

Register reusable named patterns (boot / route file / service provider):

```php
use Pinoox\Portal\Route;
use function Pinoox\Router\{pattern, patterns};

Route::pattern('username', '[a-z][a-z0-9_]{2,20}');
Route::pattern('snowflake', '[0-9]{19}');

// or batch
Route::patterns([
    'username' => '[a-z][a-z0-9_]{2,20}',
    'snowflake' => '[0-9]{19}',
]);

get('/users/{username:username}', ...);
get('/orders/{id:snowflake}', ...);
```

Helpers:

| API | Role |
|-----|------|
| `Route::pattern($name, $regex)` / `pattern()` | Register one |
| `Route::patterns([...])` / `patterns()` | Register many |
| `Route::hasPattern($name)` | Check (includes built-ins) |
| `Route::clearPatterns()` | Drop customs; restore built-ins |

Unknown `:constraint` values are treated as a **raw regex** for that parameter (escape carefully). Explicit `->filters([...])` on a route still wins over compiled types for the same key.

---

## Matching priority

Compiled specificity (approximate order):

1. **Exact** paths (no `{…}`)  
2. **Typed / enum / required** parameters  
3. **Optional** parameters  
4. **Catch-all** (`{path*}`)  
5. **Fallback** (`*` / `Route::fallback`)  

Register specific routes **before** broad catch-alls when intent matters; the score system already prefers exact and typed matches over wildcards.

---

## Performance notes

- `PathCompiler` runs at **route registration**, not on every request.
- Built-in and custom patterns live in a static registry (`ParameterPatterns`).
- Matching stays on Symfony’s compiled route map — no per-request regex rebuild for types.
- Prefer built-in types / enums over ad-hoc `filters()` for common cases.

---

## Best practices

1. Prefer `{id:int}` / enums over hand-written regex in everyday routes.  
2. Use `{path*}` for docs, downloads, and nested assets — not for every list endpoint.  
3. Keep catch-alls **last** in a group; pair with [Fallback Routes](./fallback-routes.md) for true 404 handlers.  
4. Resolve models with [Route Resolver](./route-resolver.md) after the parameter has matched (`{user:int}` + `Route::resolve('user', …)`).  
5. Read params with `$request->route('name')` for consistency with the rest of the HTTP API.

---

## Backward compatibility

- Plain `{id}` routes behave as before.  
- Existing `->filters()` / `->defaults()` continue to work.  
- Fallback `*` routes are unchanged.  
- No breaking changes to the public router API — this is an additive path syntax and pattern registry.
