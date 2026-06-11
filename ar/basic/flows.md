# Flow (الوسيط)

[← العودة إلى الفهرس](../README.md)

Flow هو طبقة الوسيط (middleware) في Pinoox: تُنفَّذ قبل إجراء المتحكم. استخدمها للإقلاع، المصادقة، التفويض، وغيرها من الاهتمامات المشتركة.

---

## Flow على مستوى التطبيق — الدالة `before()`

للإقلاع والإعداد (الجلسة، بيانات View العامة، إلخ) استخدم **`before(Request $request)`**:

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Flow\Flow;
use Pinoox\Component\Http\Request;
use Pinoox\Portal\View;

class BootFlow extends Flow
{
    protected function before(Request $request): void
    {
        View::set('siteName', config('app.name'));
    }
}
```

المسار: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Flow للمصادقة — ورّث `AuthFlow`

لحراس تسجيل الدخول، ورّث **`Pinoox\Flow\AuthFlow`** ونفّذ **`exit()`**:

```php
<?php

namespace App\com_acme_shop\Flow;

use Pinoox\Component\Http\Request;
use Pinoox\Component\Router\Route;
use Pinoox\Flow\AuthFlow;
use Pinoox\Portal\Auth;

class ShopAuthFlow extends AuthFlow
{
    protected function before(Request $request): void
    {
        Auth::boot();
    }

    protected function exit(Request $request, Route $route)
    {
        return redirect(url('login'));
    }
}
```

عندما يكون المستخدم ضيفًا، يستدعي `AuthFlow` الدالة `exit()`. لـ APIs يمكنك إرجاع خطأ JSON:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## Flow مخصص مع `handle()`

عند الحاجة، تجاوز **`handle(Request $request, Closure $next)`** مباشرة:

```php
protected function handle(Request $request, \Closure $next)
{
    if (!$this->userCanAccess($request)) {
        return response()->json(['error' => 'Forbidden'], 403);
    }

    return $next($request);
}
```

---

## تسجيل الأسماء المستعارة في app.php

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // runs on every route in the app
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — Flows على مستوى التطبيق (تُنفَّذ دائمًا)
- **`alias`** — أسماء قصيرة للاستخدام على المسارات

---

## أسماء مستعارة متداخلة (نمط المدير)

يمكن تداخل أسماء Flow المستعارة للتجميع:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

في manifest الـ API استخدم المفتاح **`manager.auth`**:

```php
// routes/api/private.php
[
    'method' => 'GET',
    'uri' => '/user/get',
    'action' => [UserController::class, 'get'],
    'name' => 'user.get',
    'flow' => ['manager.auth'],
],
```

---

## تطبيق Flow على مسار

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

عدة Flows:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Flow على مجموعة مسارات

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## إيقاف السلسلة

إذا أعاد Flow استجابة HTTP (إعادة توجيه، JSON خطأ، إلخ)، لا يُنفَّذ إجراء المتحكم.

---

## إرشادات

- تنتمي Flows إلى مجلد `Flow/` في التطبيق — وليس في pincore
- سجّل الأسماء المستعارة دائمًا في `app.php`
- إقلاع التطبيق: `before()` في manifest `flow`
- حارس تسجيل الدخول: `AuthFlow` + `exit()`

---

## وثائق ذات صلة

- [المُوجّه (Router)](./routers.md)
- [المتحكمات (Controllers)](./controllers.md)
- [الطلب (Request)](./requests.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)
