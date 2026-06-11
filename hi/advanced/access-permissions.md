# एक्सेस और अनुमतियाँ (Access & permissions)

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

Pinoox 3.x में Authorization **`Access`** Portal और `app.php` सेटिंग्स का उपयोग करता है — roles, groups, और route/API अनुमतियाँ।

---

## Helpers

```php
use Pinoox\Portal\Access;

can('blog.posts.edit');
cannot('blog.posts.delete');
Access::authorize('blog.posts.edit');
```

---

## app.php कॉन्फ़िग

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

- **`super_roles`**: मेल खाने वाला `group_key` या role → पूर्ण एक्सेस।
- **`groups`**: `UserModel.group_key` → अनुमतियों की सूची का मैपिंग (वाइल्डकार्ड जैसे `blog.*`)।

---

## Routes को सुरक्षित करना

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

जब `permission` सेट होता है, तो **`permission`** flow auth के बाद स्वचालित रूप से जोड़ दिया जाता है।

### Fluent router

```php
get('admin/posts', '@post.list')
    ->flows(['auth'])
    ->permission('blog.posts.view')
    ->name('admin.posts');
```

---

## कस्टम नियम (Custom rules)

```php
Access::define('blog.posts.publish', function ($user, $post) {
    return $user && ($user->group_key === 'admin' || $post->author_id === $user->user_id);
});
```

---

## नेस्टेड flow aliases

Manager-शैली के ऐप्स में:

```php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

Routes पर: `'flow' => ['manager.auth']`।

---

## संबंधित दस्तावेज़

- [Flows](../basic/flows.md)
- [User management](./user-management.md)
- [Token management](./token-management.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)
