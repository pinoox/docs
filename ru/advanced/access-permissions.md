# Доступ и разрешения (Access & permissions)

[← Назад к оглавлению](../README.md)

Авторизация в Pinoox 3.x использует портал **`Access`** и настройки в `app.php` — роли, группы и разрешения для маршрутов (Route) и API.

---

## Хелперы (Helpers)

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## Конфигурация app.php

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

- **`super_roles`**: совпадение `group_key` или роли → полный доступ.
- **`groups`**: сопоставление `UserModel.group_key` → список разрешений (поддерживаются шаблоны вида `blog.*`).

---

## Защита маршрутов

### API-манифест

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

Когда задано `permission`, flow **`permission`** добавляется автоматически после auth.

### Fluent-роутер

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Пользовательские правила

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Вложенные алиасы flow

В приложениях типа «менеджер»:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

В маршрутах: `'flow' => ['manager.auth']`.

---

## Связанные документы

- [Flows](../basic/flows.md)
- [Управление пользователями](./user-management.md)
- [Управление токенами](./token-management.md)

---

[← Назад к оглавлению](../README.md)
