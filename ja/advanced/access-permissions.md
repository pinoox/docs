# アクセスと権限

[← 索引に戻る](../README.md)

Pinoox 3.x の認可は **`Access`** Portal と `app.php` 設定 — ロール、グループ、ルート/API 権限を使用します。

---

## ヘルパー

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## app.php 設定

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

- **`super_roles`**: 一致する `group_key` またはロール → フルアクセス
- **`groups`**: `UserModel.group_key` → 権限リスト（`blog.*` などのワイルドカード）

---

## ルートを保護

### API マニフェスト

```php
$register->apiRoute([
    'method' => 'GET',
    'uri' => '/posts',
    'action' => [PostController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'blog.posts.view',
]);
```

`permission` が設定されると、認証後に **`permission`** flow が自動追加されます。

### Fluent router

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## カスタムルール

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## ネストされた flow エイリアス

Manager スタイルのアプリ:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

ルート上: `'flow' => ['manager.auth']`。

---

## 関連ドキュメント

- [Flows](../basic/flows.md)
- [ユーザー管理](./user-management.md)
- [トークン管理](./token-management.md)

---

[← 索引に戻る](../README.md)
