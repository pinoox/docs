# CORS

[← Back to index](../README.md)

Pinoox ships a first-class **CORS** engine for cross-origin HTTP access. It integrates with **Flow** (`cors:api`), works on any Symfony/`Response`, and stays independent of Laravel-style middleware packages.

> Prefer **`Pinoox\Portal\Cors`** and **`Pinoox\Component\Cors\CorsPolicy`**. On routes use the **`cors:`** Flow alias.

---

## Overview

| Piece | Role |
|-------|------|
| `Cors` portal | `define`, `default`, `resolve`, `apply`, `handlePreflight`, … |
| `CorsPolicy` | Fluent policy builder |
| `CorsFlow` | HTTP Flow — alias `cors:api` |
| `config/cors.config.php` | Default policy name |

---

## Basic usage

Register policies in app `boot.php` (or a service):

```php
use Pinoox\Component\Cors\CorsPolicy;
use Pinoox\Portal\Cors;

Cors::define('api', function () {
    return CorsPolicy::make()
        ->allowOrigins([
            'https://example.com',
            '*.example.com',
        ])
        ->allowMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'])
        ->allowHeaders(['*'])
        ->exposeHeaders([
            'X-RateLimit-Remaining',
            'X-Request-Id',
        ])
        ->allowCredentials()
        ->maxAge(86400);
});
```

Attach to routes:

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'api', 'flows' => ['cors:api', 'throttle:api']], function () {
    get('/products', [ProductController::class, 'index']);
});
```

---

## Named policies

```php
Cors::define('public', fn () => CorsPolicy::make()
    ->allowOrigins('*')
    ->allowMethods('*')
    ->allowHeaders('*'));

Cors::define('admin', fn () => CorsPolicy::make()
    ->allowOrigins(['https://admin.example.com'])
    ->allowMethods(['GET', 'POST'])
    ->allowCredentials());
```

```php
->flow('cors:public')
->flow('cors:admin')
->flow([CorsFlow::for('api')])
```

---

## Global / default policy

```php
Cors::default(function () {
    return CorsPolicy::make()
        ->allowOrigins('*')
        ->allowMethods('*')
        ->allowHeaders('*');
});

// or point at an existing name:
Cors::default('api');
```

Config (`pincore/config/cors.config.php`):

```php
return [
    'default' => env('CORS_DEFAULT_POLICY', 'default'),
];
```

`flow('cors')` (no name) and `Cors::apply($response)` use the default policy. The portal also registers a permissive built-in `default` policy if you have not defined one yet.

---

## Flow integration

```php
Route::flow([
    'cors:api',
    'auth',
    'throttle:api',
]);

// or
->flow('cors:api');
```

`CorsFlow`:

1. Resolves the named (or default) policy  
2. Validates `Origin`  
3. On CORS preflight (`OPTIONS` + `Access-Control-Request-Method`) → **204** with headers, **controller does not run**  
4. Otherwise runs the stack and applies headers to the response  

---

## Preflight requests

Browsers send preflight automatically. With `cors:…` on the route:

- Method: `OPTIONS`
- Response: **204 No Content**
- Headers include `Allow-Origin`, `Allow-Methods`, `Allow-Headers`, optional `Max-Age` / `Credentials`
- No controller / action execution

---

## Dynamic origins

```php
Cors::define('tenant', function () {
    return CorsPolicy::make()
        ->allowOrigins(function (string $origin, $request) {
            return Tenant::hasOrigin($origin);
        })
        ->allowMethods(['GET', 'POST'])
        ->allowCredentials();
});
```

---

## Wildcards

| Pattern | Matches |
|---------|---------|
| `*` | Any origin (cannot combine with credentials as `*`) |
| `https://example.com` | Exact origin |
| `*.example.com` | `app.example.com`, `api.example.com`, … |
| `https://*.example.com` | Scheme + host wildcard |

When **credentials** are enabled, Pinoox echoes the request `Origin` instead of `*` (browser requirement).

---

## Headers

Generated as needed:

- `Access-Control-Allow-Origin`
- `Access-Control-Allow-Methods`
- `Access-Control-Allow-Headers`
- `Access-Control-Allow-Credentials`
- `Access-Control-Max-Age`
- `Access-Control-Expose-Headers`
- `Vary: Origin`

---

## Manual usage

Apply outside routing — downloads, streams, custom responses:

```php
use Pinoox\Portal\Cors;

$response = response($data);
Cors::apply($response);           // default policy
Cors::apply($response, 'api');    // named policy

// with an explicit request:
Cors::apply($response, $request, 'api');
```

---

## Policy builder reference

```php
CorsPolicy::make()
    ->allowOrigins([...|'*'|callable])
    ->allowMethods([...|'*'])
    ->allowHeaders([...|'*'])
    ->exposeHeaders([...])
    ->allowCredentials(true)
    ->maxAge(86400);
```

---

## Examples

### SPA + API

```php
Cors::define('spa', fn () => CorsPolicy::make()
    ->allowOrigins(['https://app.example.com'])
    ->allowMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'])
    ->allowHeaders(['Authorization', 'Content-Type', 'X-Requested-With'])
    ->exposeHeaders(['X-Request-Id'])
    ->allowCredentials()
    ->maxAge(600));

group(['flows' => ['cors:spa', 'auth']], function () { /* ... */ });
```

### Public CDN-style API

```php
Cors::define('open', fn () => CorsPolicy::make()
    ->allowOrigins('*')
    ->allowMethods(['GET', 'HEAD', 'OPTIONS'])
    ->allowHeaders(['*']));
```

---

## Best practices

1. Prefer **explicit origins** in production; avoid `*` with cookies/auth.
2. Put **`cors:` before `auth`** when preflight must succeed without credentials.
3. Expose only headers the frontend needs (`exposeHeaders`).
4. Set a sensible **`maxAge`** to reduce preflight traffic.
5. For multi-tenant apps, use a **callback** origin checker — never reflect arbitrary `Origin` blindly.
6. Combine with [Rate Limiter](./rate-limiter.md) (`throttle:api`) on public APIs.

---

## Related docs

- [Flow](../basic/flows.md)
- [Router](../basic/routers.md)
- [Rate Limiter](./rate-limiter.md)
- [HTTP client](./http-client.md)

---

[← Back to index](../README.md)
