# Flow (middleware)

[← इंडेक्स पर वापस जाएँ](../README.md)

Flow Pinoox की middleware परत है: यह controller action से पहले चलती है। इसका उपयोग bootstrapping, प्रमाणीकरण (authentication), प्राधिकरण (authorization) और इसी तरह की cross-cutting ज़रूरतों के लिए करें।

---

## ऐप-व्यापी Flow — `before()` method

Boot और सेटअप (session, ग्लोबल View डेटा, आदि) के लिए **`before(Request $request)`** का उपयोग करें:

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

पाथ: `apps/com_acme_shop/Flow/BootFlow.php`

---

## Auth Flow — `AuthFlow` को extend करें

लॉगिन guards के लिए, **`Pinoox\Flow\AuthFlow`** को extend करें और **`exit()`** implement करें:

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

जब उपयोगकर्ता guest होता है, तो `AuthFlow` `exit()` कॉल करता है। APIs के लिए आप एक JSON त्रुटि लौटा सकते हैं:

```php
use Pinoox\Component\Http\Api\ApiResponse;

protected function exit(Request $request, Route $route)
{
    return ApiResponse::error('ACCESS_DENIED', 'Access denied.', status: 401);
}
```

---

## `handle()` के साथ कस्टम Flow

आवश्यकता होने पर, **`handle(Request $request, Closure $next)`** को सीधे override करें:

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

## app.php में उपनाम (aliases) पंजीकृत करें

```php
<?php

use App\com_acme_shop\Flow\BootFlow;
use App\com_acme_shop\Flow\ShopAuthFlow;

return [
    'package' => 'com_acme_shop',
    // ...
    'flow' => [
        BootFlow::class,   // ऐप के हर route पर चलता है
    ],
    'alias' => [
        'auth' => ShopAuthFlow::class,
    ],
];
```

- **`flow`** — ऐप-व्यापी Flows (हमेशा निष्पादित)
- **`alias`** — routes पर उपयोग के लिए छोटे नाम

---

## नेस्टेड उपनाम (मैनेजर पैटर्न)

Flow उपनामों को समूहीकरण के लिए नेस्ट किया जा सकता है:

```php
// apps/com_pinoox_manager/app.php
'alias' => [
    'manager' => [
        'auth' => ManagerAuthFlow::class,
    ],
],
```

API मैनिफ़ेस्ट में **`manager.auth`** key का उपयोग करें:

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

## किसी route पर Flow लागू करें

```php
use function Pinoox\Router\get;

get('dashboard', '@dashboard')
    ->flows(['auth'])
    ->name('dashboard');
```

एकाधिक Flows:

```php
get('admin', [AdminController::class, 'index'])
    ->flows(['auth', 'admin'])
    ->name('admin');
```

---

## Route समूह पर Flow

```php
use function Pinoox\Router\{get, group};

group(['prefix' => 'account', 'flows' => ['auth']], function () {
    get('profile', [UserController::class, 'profile'])->name('account.profile');
    get('settings', [UserController::class, 'settings'])->name('account.settings');
});
```

---

## चेन को रोकना

यदि कोई Flow एक HTTP response (redirect, error JSON, आदि) लौटाता है, तो controller action नहीं चलता।

---

## दिशानिर्देश

- Flows ऐप के `Flow/` फ़ोल्डर में रहते हैं — pincore में नहीं
- उपनाम हमेशा `app.php` में पंजीकृत करें
- ऐप-व्यापी boot: `flow` मैनिफ़ेस्ट में `before()`
- लॉगिन guard: `AuthFlow` + `exit()`

---

## संबंधित दस्तावेज़

- [Router](./routers.md)
- [Controllers](./controllers.md)
- [Request](./requests.md)
- [प्रोजेक्ट संरचना](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
