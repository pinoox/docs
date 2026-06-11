# Acceso y permisos

[← Volver al índice](../README.md)

La autorización en Pinoox 3.x usa el portal **`Access`** y la configuración de `app.php` — roles, grupos y permisos de rutas/API.

---

## Helpers

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## Configuración app.php

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

- **`super_roles`**: si coincide `group_key` o rol → acceso completo.
- **`groups`**: mapea `UserModel.group_key` → lista de permisos (comodines como `blog.*`).

---

## Proteger rutas

### Manifiesto API

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

Cuando se define `permission`, el flow **`permission`** se añade automáticamente después de auth.

### Router fluido

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Reglas personalizadas

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Alias de flows anidados

En apps de estilo manager:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

En rutas: `'flow' => ['manager.auth']`.

---

## Documentación relacionada

- [Flows](../basic/flows.md)
- [Gestión de usuarios](./user-management.md)
- [Gestión de tokens](./token-management.md)

---

[← Volver al índice](../README.md)
