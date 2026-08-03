# Route Resolver

[← بازگشت به فهرست](../README.md)

پینوکس می‌تواند **پارامترهای route را قبل از کنترلر به آبجکت** تبدیل کند — مدل، tenant، locale یا هر نوع سفارشی. این موتور بومی **Route Resolver** است.

> ترجیحاً از **`Route::resolve()`** / **`Pinoox\Portal\RouteResolver`** استفاده کنید و فلو **`resolve`** (یا `ResolveFlow::class`) را به زنجیره اضافه کنید.

---

## نمای کلی

| قطعه | نقش |
|------|-----|
| `ResolverManager` | رجیستری bindingها |
| `Binding` | `->missing(...)` |
| `ModelResolver` | بارگذاری مدل با route key |
| `ResolveFlow` | alias: `resolve` |

---

## ثبت resolver

```php
use Pinoox\Portal\Route;

Route::resolve('user', User::class);
Route::resolve('post', Post::class);

Route::resolve('tenant', function ($value) {
    return TenantService::findByDomain($value);
});
```

---

## مدل و route key

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

کنترلر بدون `find()` دستی:

```php
public function show(User $user)
{
    return view('user.show', compact('user'));
}
```

---

## اتصال Flow

```php
get('users/{user}', [UserController::class, 'show'])
    ->flow(['resolve', 'auth']);

group(['flows' => ['resolve', 'auth', 'throttle:api']], function () {
    // ...
});
```

---

## missing

پیش‌فرض: **۴۰۴**. سفارشی:

```php
Route::resolve('user', User::class)
    ->missing(fn () => redirect('/'));
```

---

## بهترین تمرین‌ها

1. Binding را یک‌بار در boot ثبت کنید.
2. برای injection مبتنی بر binding حتماً فلو `resolve` را بگذارید.
3. نام پارامتر، binding و آرگومان کنترلر را یکی نگه دارید.
4. برای شناسهٔ عمومی از uuid/slug استفاده کنید.

---

## مستندات مرتبط

- [روتر](../basic/routers.md)
- [فلو](../basic/flows.md)
- [کنترلر](../basic/controllers.md)

---

[← بازگشت به فهرست](../README.md)
