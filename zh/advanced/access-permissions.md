# 访问与权限（Access & permissions）

[← 返回索引](../README.md)

Pinoox 3.x 的授权使用 **`Access`** Portal 和 `app.php` 设置 —— 涵盖角色（Role）、用户组以及路由/API 权限。

---

## 辅助函数

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## app.php 配置

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

- **`super_roles`**：匹配的 `group_key` 或角色 → 拥有全部权限。
- **`groups`**：将 `UserModel.group_key` 映射到权限列表（支持 `blog.*` 这样的通配符）。

---

## 保护路由

### API 清单（manifest）

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

设置了 `permission` 后，**`permission`** flow 会在 auth 之后自动添加。

### 链式路由（Fluent router）

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## 自定义规则

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## 嵌套 flow 别名

在 manager 风格的应用中：

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

在路由上使用：`'flow' => ['manager.auth']`。

---

## 相关文档

- [Flow](../basic/flows.md)
- [用户管理（User management）](./user-management.md)
- [令牌管理（Token management）](./token-management.md)

---

[← 返回索引](../README.md)
