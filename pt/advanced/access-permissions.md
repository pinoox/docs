# Acesso e Permissões

[← Voltar ao índice](../README.md)

A autorização no Pinoox 3.x usa o portal **`Access`** e configurações do `app.php` — papéis (roles), grupos e permissões de rota/API.

---

## Helpers

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## Configuração no app.php

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

- **`super_roles`**: `group_key` ou role correspondente → acesso total.
- **`groups`**: mapeia `UserModel.group_key` → lista de permissões (curingas como `blog.*`).

---

## Proteger rotas

### Manifest da API

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

Quando `permission` é definido, o flow **`permission`** é adicionado automaticamente após o auth.

### Router fluente

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Regras personalizadas

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Aliases de flow aninhados

Em apps no estilo manager:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Nas rotas: `'flow' => ['manager.auth']`.

---

## Documentação relacionada

- [Flows](../basic/flows.md)
- [Gerenciamento de usuários](./user-management.md)
- [Gerenciamento de tokens](./token-management.md)

---

[← Voltar ao índice](../README.md)
