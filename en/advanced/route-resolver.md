# Route Resolver

[← Back to index](../README.md)

Pinoox can **resolve route parameters into objects** before the controller runs — models, tenants, locales, or any custom type. This is the native **Route Resolver** engine (not Laravel-style middleware).

> Prefer **`Route::resolve()`** / **`Pinoox\Portal\RouteResolver`** and attach **`resolve`** (or `ResolveFlow::class`) in the Flow chain.

---

## Overview

| Piece | Role |
|-------|------|
| `ResolverManager` | Registry: `define` / `bind` / `resolve` / `resolveParameter` |
| `Binding` | Fluent handle — `->missing(...)` |
| `ModelResolver` | Load Eloquent models by route key |
| `CallbackResolver` | Wrap callables |
| `ResolveFlow` | Flow alias `resolve` |
| `ModelValueResolver` | DI fallback for type-hinted models |

---

## Registering resolvers

Typically in `boot.php` or a service:

```php
use App\com_acme_shop\Model\User;
use App\com_acme_shop\Model\Post;
use Pinoox\Portal\Route;

Route::resolve('user', User::class);
Route::resolve('post', Post::class);
```

Equivalent:

```php
use Pinoox\Portal\RouteResolver;

RouteResolver::define('user', User::class);
RouteResolver::bind('post', Post::class);
```

---

## Model resolvers

Models extend `Pinoox\Component\Database\Model`. Configure the column used for lookup:

```php
class User extends Model
{
    protected static string $routeKey = 'uuid';
}

class Post extends Model
{
    protected static string $routeKey = 'slug';
}
```

`ModelResolver` runs:

```sql
SELECT * FROM … WHERE {routeKey} = {parameter} LIMIT 1
```

Default route key is Eloquent’s `getRouteKeyName()` (`id`).

Controller — no manual `find()`:

```php
public function show(User $user)
{
    return view('user.show', compact('user'));
}
```

Route: `/users/{user}` + binding `user` → `User::class`.

---

## Custom resolvers

### Callable

```php
Route::resolve('tenant', function ($value, string $parameter, $request) {
    return TenantService::findByDomain($value);
});
```

### Resolver class

```php
use Pinoox\Component\RouteResolver\Resolver;
use Pinoox\Component\Http\Request;

class TenantResolver extends Resolver
{
    public function resolve(mixed $value, string $parameter, Request $request): mixed
    {
        return TenantService::findByDomain((string) $value);
    }
}

Route::resolve('tenant', TenantResolver::class);
```

Future domains (workspace, locale, currency, version, …) are just more named bindings — no core changes required.

---

## Flow integration

Resolvers run only when **`ResolveFlow`** is in the chain:

```php
use Pinoox\Flow\ResolveFlow;
use function Pinoox\Router\{get, group};

get('users/{user}', [UserController::class, 'show'])
    ->flow(['resolve', 'auth'])
    ->name('users.show');

group(['flows' => [ResolveFlow::class, 'auth', 'throttle:api']], function () {
    get('posts/{post}', [PostController::class, 'show']);
});
```

Alias **`resolve`** is registered by the core (same as `throttle`, `cors`, `session`).

Order tip: put **`resolve` before `auth`** when guests may hit public resource URLs; put it after auth when resolution depends on the current user.

---

## Missing handlers

Default failure → **HTTP 404** (`NotFoundHttpException`).

Override per binding:

```php
Route::resolve('user', User::class)
    ->missing(function ($value, string $parameter, $request) {
        return redirect('/');
    });
```

If `missing` returns a `Response`, the controller does not run.

---

## Automatic model injection (without ResolveFlow)

When the controller type-hints a Model and the route attribute is still a scalar, **`ModelValueResolver`** loads the model by `routeKeyName()`. Prefer explicit `Route::resolve()` + `resolve` Flow for custom types and `missing` handlers.

---

## Examples

### UUID users

```php
class User extends Model
{
    protected static string $routeKey = 'uuid';
}

Route::resolve('user', User::class);

get('u/{user}', [UserController::class, 'show'])
    ->flow('resolve');
```

### Slug posts + custom 404 page

```php
Route::resolve('post', Post::class)
    ->missing(fn () => view('errors/post-missing'));

get('blog/{post}', [BlogController::class, 'show'])->flow(['resolve']);
```

### Multi-tenant domain key

```php
Route::resolve('tenant', TenantResolver::class);

group(['prefix' => '{tenant}', 'flows' => ['resolve', 'auth']], function () {
    get('/dashboard', [DashboardController::class, 'index']);
});
```

---

## Best practices

1. Register bindings once at boot — not inside every request.
2. Always attach **`resolve`** (or `ResolveFlow`) on routes that need object injection via bindings.
3. Keep parameter names aligned: `{user}` ↔ `Route::resolve('user', …)` ↔ `show(User $user)`.
4. Use `$routeKey` for public IDs (uuid/slug); keep numeric PK internal.
5. Prefer `missing()` redirects for web UIs; JSON APIs can rely on default 404.
6. Do not put heavy logic in resolvers — delegate to services.

---

## Related docs

- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)
- [Controllers](../basic/controllers.md)
- [Eloquent ORM](../eloquent-orm/getting-started.md)

---

[← Back to index](../README.md)
