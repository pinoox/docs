# دسترسی و مجوز (Access)

[← بازگشت به فهرست](../../readme-fa.md)

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

## مستندات مرتبط

- [فلو — Flow](../basic/flows.md)
- [مدیریت کاربران](./user-management.md)
- [توکن — Token](./token-management.md)

---

[← بازگشت به فهرست](../../readme-fa.md)
