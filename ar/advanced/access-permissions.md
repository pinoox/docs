# الوصول والصلاحيات (Access & Permissions)

[← العودة إلى الفهرس](../README.md)

تعتمد آلية التفويض (Authorization) في Pinoox 3.x على بوابة **`Access`** وإعدادات `app.php` — الأدوار (Roles)، والمجموعات (Groups)، وصلاحيات المسارات (Routes) وواجهات API.

---

## الدوال المساعدة (Helpers)

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## إعدادات app.php

```php
'transport' => [
    'user' => 'platform',
    'access' => 'platform',
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

- **`super_roles`**: عند تطابق `group_key` أو الدور (Role) → وصول كامل.
- **`groups`**: ربط `UserModel.group_key` ← بقائمة الصلاحيات (مع دعم أحرف البدل مثل `blog.*`).

---

## حماية المسارات (Routes)

### بيان API (API manifest)

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

عند تعيين `permission`، تتم إضافة تدفق **`permission`** تلقائياً بعد المصادقة (auth).

### الموجّه الانسيابي (Fluent router)

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## قواعد مخصّصة (Custom rules)

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## أسماء بديلة متداخلة للتدفقات (Nested flow aliases)

في التطبيقات من نمط المدير (manager):

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

على المسارات: `'flow' => ['manager.auth']`.

---

## وثائق ذات صلة

- [التدفقات (Flows)](../basic/flows.md)
- [إدارة المستخدمين](./user-management.md)
- [إدارة الرموز (Tokens)](./token-management.md)

---

[← العودة إلى الفهرس](../README.md)
