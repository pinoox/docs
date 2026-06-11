# Accès et permissions

[← Retour à l'index](../README.md)

L'autorisation dans Pinoox 3.x utilise le portail **`Access`** et les paramètres de `app.php` — rôles, groupes et permissions de routes/API.

---

## Helpers

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## Configuration app.php

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

- **`super_roles`** : si le `group_key` ou le rôle correspond → accès complet.
- **`groups`** : associe `UserModel.group_key` → liste de permissions (jokers comme `blog.*`).

---

## Protéger les routes

### Manifeste API

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

Lorsque `permission` est défini, le flow **`permission`** est ajouté automatiquement après l'authentification.

### Routeur fluide (fluent router)

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Règles personnalisées

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Alias de flow imbriqués

Dans les applications de type manager :

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Sur les routes : `'flow' => ['manager.auth']`.

---

## Documentation associée

- [Flows](../basic/flows.md)
- [Gestion des utilisateurs](./user-management.md)
- [Gestion des tokens](./token-management.md)

---

[← Retour à l'index](../README.md)
