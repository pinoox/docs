# Access & permissions

[← Back to index](../README.md)

Authorization in Pinoox 3.x uses the **`Access`** portal and `app.php` settings — roles, groups, and route/API permissions.

---

## Helpers

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

- **`super_roles`**: matching `group_key` or role → full access.
- **`groups`**: map `UserModel.group_key` → permission list (wildcards like `blog.*`).

---

## Protect routes

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

When `permission` is set, the **`permission`** flow is added automatically after auth.

### Fluent router

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Custom rules

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Nested flow aliases

In manager-style apps:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

On routes: `'flow' => ['manager.auth']`.

---

## CLI (terminal)

Roles and permissions are stored in the database when using platform/shared access transport. Manage them without custom scripts:

| Command | Purpose |
|---------|---------|
| `role:list` / `role:create` / `role:show` / `role:update` / `role:delete` | Role CRUD |
| `permission:list` / `permission:create` / `permission:show` / `permission:delete` | Permission CRUD |
| `role:permission {role}` | `--attach` / `--detach` permission keys on a role |
| `user:role {user}` | Assign roles to a user |

Aliases: `roles`, `permissions`, `make:permission`.

```bash
php pinoox permission:create com_my_shop blog.posts.view --name="View posts"
php pinoox role:create com_my_shop --key=editor --name=Editor
php pinoox role:permission editor --attach=blog.posts.view --attach=blog.posts.edit
php pinoox user:role 5 --attach=editor
```

Permission keys must match `[a-z0-9][a-z0-9_.*\-]*` (wildcards like `blog.*` are allowed in route checks via `Access` config groups).

See [CLI reference](../start/cli-reference.md) and [User management](./user-management.md).

---

## Related docs

- [Flows](../basic/flows.md)
- [User management](./user-management.md)
- [Token management](./token-management.md)

---

[← Back to index](../README.md)
