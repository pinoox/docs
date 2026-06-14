# دسترسی و مجوز (Access)

[← بازگشت به فهرست](../README.md)

کنترل دسترسی در پینوکس ۳.x از **Portal `Access`** و تنظیمات `app.php` انجام می‌شود — نقش‌ها، گروه‌ها، و مجوز روی route/API.

---

## Helperها

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');                    // bool
cannot('blog.posts.delete');               // bool
Access::authorize('blog.posts.edit');      // خطا اگر مجاز نباشد
```

---

## تنظیم app.php

```php
'transport' => [
    'user' => 'platform',
    'access' => 'platform',   // اختیاری
],
'access' => [
    'enabled' => true,
    'super_roles' => ['admin', 'superadmin'],
    'groups' => [
        'admin' => ['*'],
        'editor' => ['blog.posts.view', 'blog.posts.edit'],
    ],
],
```

- **`super_roles`**: کاربر با `group_key` یا role مناسب → دسترسی کامل.
- **`groups`**: map `UserModel.group_key` → لیست permission (با wildcard `blog.*`).

---

## محافظت route

### Flow + permission روی API

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

وقتی `permission` تنظیم شود، Flow **`permission`** خودکار اضافه می‌شود (بعد از auth).

### Fluent router

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## define سفارشی

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Flow alias تو در تو

در اپ manager:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

روی route: `'flow' => ['manager.auth']`.

---

## CLI (ترمینال)

نقش‌ها و permissionها در دیتابیس (با transport مشترک) ذخیره می‌شوند:

| دستور | کاربرد |
|--------|--------|
| `role:list` / `role:create` / … | CRUD نقش |
| `permission:list` / `permission:create` / … | CRUD permission |
| `role:permission {role}` | `--attach` / `--detach` کلید permission |
| `user:role {user}` | تخصیص نقش به کاربر |

Alias: `roles`, `permissions`, `make:permission`.

```bash
php pinoox permission:create com_my_shop blog.posts.view --name="View posts"
php pinoox role:create com_my_shop --key=editor --name=Editor
php pinoox role:permission editor --attach=blog.posts.view
php pinoox user:role 5 --attach=editor
```

کلید permission باید با `[a-z0-9][a-z0-9_.*\-]*` سازگار باشد.

مرجع: [CLI](../start/cli-reference.md)، [مدیریت کاربر](./user-management.md).

---

## مستندات مرتبط

- [فلو — Flow](../basic/flows.md)
- [مدیریت کاربران](./user-management.md)
- [توکن — Token](./token-management.md)

---

[← بازگشت به فهرست](../README.md)
