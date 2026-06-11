# المُوجّه (Router)

[← العودة إلى الفهرس](../README.md)

توجيه Pinoox 3.x له طبقتان: **Named Actions** (معالجات منطقية) و**Routes** (مسارات URL وطرق HTTP). يحدّد كل تطبيق مساراته في مجلد **`routes/`** ويسجّلها في **`app.php`**.

> لا تستخدم **`Pinoox\Portal\Router::get`**. استورد دوال الموجّه من مساحة الأسماء **`Pinoox\Router`**.

---

## تسجيل ملفات المسارات في app.php

```php
'router' => [
    'routes' => [
        'routes/web.php',
        'routes/actions.php',
        'routes/api.php',
    ],
],
```

---

## استيراد دوال الموجّه

```php
use function Pinoox\Router\{
    get, post, put, patch, delete,
    action, group, routes, collect, route
};
```

الدالة **`collection()`** غير موجودة في pincore 3.x.

---

## Named Actions

عرّف المعالج مرة واحدة في `routes/actions.php`:

```php
<?php

use App\com_acme_shop\Controller\MainController;
use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\action;

action('welcome', [MainController::class, 'home']);
action('product.show', [ProductController::class, 'show']);
```

---

## ربط عناوين URL في web.php

```php
<?php

use App\com_acme_shop\Controller\ProductController;
use function Pinoox\Router\{get, post};

get('/', '@welcome')->name('home');
get('product/{id}', '@product.show')->name('product.show');
post('product', [ProductController::class, 'store'])->name('product.store');
```

- `@welcome` — مرجع إلى action مسجّل
- `{id}` — معامل ديناميكي (يُمرَّر للمتحكم أو عبر `$request->parametersOne('id')`)

---

## طرق HTTP

```php
use function Pinoox\Router\{get, post, put, patch, delete};

post('login', [AuthController::class, 'login'])->name('login');
put('product/{id}', [ProductController::class, 'update'])->name('product.update');
delete('product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
```

---

## مجموعات المسارات

استخدم `group()` لبادئة مشتركة وFlow على عدة مسارات:

```php
use App\com_acme_shop\Controller\AdminController;
use function Pinoox\Router\{get, group};

group(['prefix' => 'admin', 'flows' => ['auth']], function () {
    get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    get('orders', [AdminController::class, 'orders'])->name('admin.orders');
});
```

المسارات الناتجة: `/admin/dashboard`، `/admin/orders`

---

## Flow على مسار واحد

```php
get('panel', '@dashboard')
    ->flows(['auth'])
    ->name('panel');
```

يجب تسجيل الاسم المستعار المقابل في `app.php` ضمن `'alias'`.

---

## ملف مسارات API — `routes()` و `collect()`

```php
<?php
// routes/api.php

use App\com_acme_shop\Controller\ProductApiController;
use function Pinoox\Router\{collect, get, post, routes};

return routes([
    'version' => 'v1',
    'prefix' => '',
    'routes' => collect(function () {
        get('/products', [ProductApiController::class, 'index'])->name('products.index');
        post('/products', [ProductApiController::class, 'store'])->name('products.store');
        get('/products/{id}', [ProductApiController::class, 'show'])->name('products.show');
    }),
]);
```

`collect()` يجمع المسارات داخل manifest API. أرجع manifest النهائي بـ **`routes([...])`**.

---

## URL من اسم المسار

```php
use function Pinoox\Router\route;

echo route('home');                    // URL of the active route
echo route('product.show', ['id' => 5]);
```

---

## Fallback (404)

```php
use Pinoox\Portal\View;
use function Pinoox\Router\get;

get('*', fn () => View::render('errors/404'))->name('fallback');
```

---

## اختيار التطبيق النشط (مستوى المشروع)

يُضبط التطبيق الذي يعالج الطلب في `config/app-router.config.php` (بادئة URL) أو `config/domain.config.php` (النطاق):

```php
// config/app-router.config.php
return [
    '/' => 'com_pinoox_welcome',
    '/shop' => 'com_acme_shop',
];
```

CLI:

```bash
php pinoox app:router set /shop com_acme_shop
```

---

## وثائق ذات صلة

- [Flow](./flows.md)
- [المتحكمات (Controllers)](./controllers.md)
- [الطلب (Request)](./requests.md)
- [تطبيقك الأول](../start/your-first-app.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)
