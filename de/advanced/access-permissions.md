# Zugriff & Berechtigungen

[← Zurück zur Übersicht](../README.md)

Die Autorisierung in Pinoox 3.x verwendet das **`Access`**-Portal und Einstellungen in `app.php` — Rollen, Gruppen und Routen-/API-Berechtigungen.

---

## Helpers

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## Konfiguration in app.php

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

- **`super_roles`**: passender `group_key` oder Rolle → Vollzugriff.
- **`groups`**: Zuordnung `UserModel.group_key` → Berechtigungsliste (Wildcards wie `blog.*`).

---

## Routen schützen

### API-Manifest

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

Wenn `permission` gesetzt ist, wird der **`permission`**-Flow automatisch nach auth hinzugefügt.

### Fluent-Router

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Eigene Regeln

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## Verschachtelte Flow-Aliasse

In Apps im Manager-Stil:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Auf Routen: `'flow' => ['manager.auth']`.

---

## Verwandte Dokumente

- [Flows](../basic/flows.md)
- [Benutzerverwaltung (User management)](./user-management.md)
- [Token-Verwaltung](./token-management.md)

---

[← Zurück zur Übersicht](../README.md)
