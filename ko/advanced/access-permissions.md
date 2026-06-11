# Access & permissions

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x 권한 부여는 **`Access`** portal과 `app.php` 설정 — role, group, route/API permission을 사용합니다.

---

## Helper

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## app.php config

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

- **`super_roles`**: 일치하는 `group_key` 또는 role → 전체 access.
- **`groups`**: `UserModel.group_key` → permission list 매핑 (`blog.*` 같은 wildcard).

---

## Route 보호

### API manifest

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

`permission`이 설정되면 auth 후 **`permission`** flow가 자동 추가됩니다.

### Fluent router

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Custom rule

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Nested flow alias

Manager-style 앱에서:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Route에서: `'flow' => ['manager.auth']`.

---

## 관련 문서

- [Flows](../basic/flows.md)
- [User management](./user-management.md)
- [Token management](./token-management.md)

---

[← 색인으로 돌아가기](../README.md)
