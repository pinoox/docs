# Erişim ve izinler

[← Dizine dön](../README.md)

Pinoox 3.x'te yetkilendirme **`Access`** portal'ı ve `app.php` ayarlarını kullanır — roller, gruplar ve route/API izinleri.

---

## Helper'lar

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

- **`super_roles`**: eşleşen `group_key` veya rol → tam erişim.
- **`groups`**: `UserModel.group_key` → izin listesi eşlemesi (`blog.*` gibi wildcard'lar).

---

## Route'ları koruma

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

`permission` ayarlandığında **`permission`** flow'u kimlik doğrulamadan sonra otomatik eklenir.

### Fluent router

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## Özel kurallar

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## İç içe flow takma adları

Manager tarzı uygulamalarda:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Route'larda: `'flow' => ['manager.auth']`.

---

## İlgili dokümantasyon

- [Flow'lar](../basic/flows.md)
- [Kullanıcı yönetimi](./user-management.md)
- [Token yönetimi](./token-management.md)

---

[← Dizine dön](../README.md)
