# boot.php و رویدادها

[← بازگشت به فهرست](../README.md)

علاوه بر `routes/`، می‌توانید در **`boot.php`** مسیر، API، Flow، schedule، GraphQL، listener و bindingهای DI ثبت کنید — مفید برای **افزونه**، micro-module، یا اتصال به اپ میزبان.

هر اپ می‌تواند `apps/{package}/boot.php` داشته باشد. این فایل یک closure برمی‌گرداند که `AppRegister` می‌گیرد و **قبل از** handle درخواست اجرا می‌شود.

---

## چرخهٔ بوت

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### مراحل pipeline

| مرحله | کار |
|-------|-----|
| `boot.global` | بوت اپ‌های `boot-global => true` در هر درخواست |
| `app.boot` | بوت اپ فعال (+ extenderها با `extends`) |

### رویدادهای boot

| نام | زمان |
|-----|------|
| `app.booting` / `app.booting.{package}` | قبل از commit |
| `app.booted` / `app.booted.{package}` | بعد از integrate |
| `app.routes` / `app.routes.{package}` | هنگام اعمال route وب |
| `app.api` / `app.api.{package}` | هنگام ساخت registry API |

گوش دادن از داخل `boot.php`:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### رویدادهای درخواست (هسته)

به‌صورت پیش‌فرض در هر درخواست HTTP توسط فریم‌ورک dispatch می‌شوند (`AppCoreEventSubscriber`):

| نام | زمان | variant پکیج | کانال نام‌دار |
|-----|------|--------------|---------------|
| `app.route.matched` | بعد از match شدن route | `app.route.matched.{package}` | `app.route.{routeName}` یا `app.api.{routeName}` |
| `app.controller` | قبل از اجرای controller | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | قبل از ارسال پاسخ | `app.response.{package}` | — |
| `app.exception` | هنگام exception | `app.exception.{package}` | — |
| `app.terminate` | بعد از ارسال پاسخ | `app.terminate.{package}` | — |

```php
use Pinoox\Component\AppEvent\AppEventNames;
use Pinoox\Component\AppEvent\AppRouteMatchedEvent;

$register->listen(AppEventNames::ROUTE_MATCHED, function (AppRouteMatchedEvent $event): void {
    // $event->request, $event->route, $event->routeName(), $event->isApi()
});

$register->listen(
    AppEventNames::route('app.run'),
    function (AppRouteMatchedEvent $event): void {},
);

$register->listen(
    AppEventNames::package(AppEventNames::CONTROLLER, $register->package()),
    $listener,
);
```

برای hook ساده از **watch** (`onRoute`, `onApi`, …) استفاده کنید؛ برای کنترل کامل از **listen** روی رویدادهای هسته.

---

## سه حالت اپ

| حالت | تنظیم | رفتار |
|------|--------|--------|
| **Route only** | فقط `router.routes` | فقط وقتی URL اپ فعال است |
| **Boot global** | `boot-global => true` | در هر درخواست HTTP boot می‌شود |
| **Boot + Route** | `boot.php` + routes | پیش‌فرض scaffold |

افزونه روی اپ میزبان:

```php
'extends' => ['com_host_app'],
```

فقط وقتی host boot شود، افزونه شما هم boot می‌شود (سبک‌تر از global).

---

## کلیدهای `app.php` برای boot

این کلیدها در `apps/{package}/app.php` مشخص می‌کنند **`boot.php` اجرا شود یا نه**، **چه زمانی** اجرا شود، و خروجی boot cache شود یا نه. آن‌ها pipeline بوت را تنظیم می‌کنند — جایگزین خود `boot.php` نیستند.

### فایل boot (`boot`)

کنترل پیدا کردن و اجرای اسکریپت boot (پیش‌فرض: `apps/{package}/boot.php`).

| مقدار | پیش‌فرض | نتیجه |
|-------|---------|--------|
| `true` | بله | اجرای `boot.php` هنگام بوت این اپ |
| `false` | | بدون boot file — فقط route |
| `'path/custom.php'` | | فایل دیگر نسبت به ریشه اپ |

```php
'boot' => true,              // استاندارد — apps/{package}/boot.php
'boot' => false,             // بدون ثبت programmatic
'boot' => 'setup/boot.php',  // اسکریپت boot سفارشی
```

فایل باید **callable برگرداند**: `fn (AppRegister $register) => …`. اگر `boot` برابر `true` باشد ولی فایل نباشد، خطا نمی‌دهد — boot رد می‌شود.

### plugin سراسری (`boot-global`)

| مقدار | پیش‌فرض | نتیجه |
|-------|---------|--------|
| `false` | بله | بوت فقط وقتی این اپ فعال است (URL match) یا extender یک host |
| `true` | | بوت در **هر درخواست HTTP**، قبل از اپ فعال |

برای plugin سراسری (log، feature flag). هر request هزینه boot این اپ را دارد — سبک نگه دارید.

### plugin روی host (`extends`)

| مقدار | پیش‌فرض | نتیجه |
|-------|---------|--------|
| `[]` | بله | اپ عادی — فقط برای خودش boot |
| `['com_host_app']` | | boot **قبل از** host وقتی آن host فعال شود |

سبک‌تر از `boot-global`: plugin فقط وقتی host اجرا می‌شود boot می‌شود (مثلاً route یا watch به پنل).

```php
'extends' => ['com_pinoox_manager'],
```

### ثبت اضافه (`startup`)

| مقدار | پیش‌فرض | نتیجه |
|-------|---------|--------|
| `null` | بله | بدون مرحله دوم |
| `fn (AppRegister $r) => …` | | ثبت اضافه در `app.php`، **بعد از** `boot.php` با همان API |

بیشتر موارد را در `boot.php` بگذارید. `startup` برای ثبت کوچک inline در manifest.

### cache boot (`cache`)

cache زمان اجرا **opt-in** است (`cache.enabled` باید `true` باشد).

| کلید | پیش‌فرض | نتیجه |
|------|---------|--------|
| `cache.enabled` | `false` | کلید اصلی — تا `true` نشود hydrate نمی‌شود |
| `cache.stores.boot` | `true` | cache ثبت‌های boot (manifest API/GraphQL، bindings) |
| `cache.stores.routes` | `true` | cache manifest route/action |
| `cache.stores.api` | `true` | cache لیست entryهای API |

```php
'cache' => [
    'enabled' => true,
    'stores' => [
        'boot' => true,
        'routes' => true,
        'api' => true,
    ],
],
```

بعد از deploy: `php pinoox cache:build {package}`. جزئیات: [cache boot](#cache-boot) و [Pinker](./pinker.md).

### انتخاب سریع

| می‌خواهید… | در `app.php` |
|------------|--------------|
| اپ عادی (route + boot) | `'boot' => true` (پیش‌فرض) |
| فقط route | `'boot' => false` |
| plugin سراسری | `'boot-global' => true` |
| plugin روی یک host | `'extends' => ['com_host_app']` |
| boot سریع‌تر در production | `'cache.enabled' => true` + `cache:build` |

---

## boot.php پایه

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => static fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => static fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## ترکیب با فایل route

`boot.php` با `routes/web.php` و `routes/api.php` با هم کار می‌کند. CRUD پایدار را در route file بگذارید؛ ثبت شرطی، plugin و hook را در `boot.php`.

---

## AppRegister — متدهای پرکاربرد

| متد | کاربرد |
|-----|--------|
| `web(callable)` | ثبت route با builder |
| `route([...])` | یک route web (با `flow`، `permission`) |
| `api([manifest])` | manifest کامل API |
| `apiRoute([...])` | یک endpoint API |
| `graphql([manifest])` | type / query / mutation |
| `action('name', handler)` | Named Action |
| `flowAlias(['auth' => AuthFlow::class])` | alias تخت |
| `alias(['myapp' => ['auth' => AuthFlow::class]])` | alias تو در تو |
| `schedule(callable)` | task زمان‌بندی |
| `listen('event', listener)` | listener رویداد |
| `subscribe(SubscriberClass::class)` | Symfony subscriber |
| `when('com_host', fn)` | hook روی boot اپ دیگر |
| `onRoute` / `onApi` / `onPath` | watch درخواست‌ها (پایین) |
| `onController` / `onAction` | watch کنترلر یا action |
| `onModel` | watch رویداد Eloquent |
| `onTheme` | فعال شدن context یا پوشهٔ theme |

---

## Watch — واکنش به route، API، controller، model

در `boot.php` بدون نوشتن Symfony subscriber:

```php
use Pinoox\Component\AppEvent\AppWatchContext;

return function (AppRegister $register): void {
    $register->onRoute('app.run', function (AppWatchContext $ctx): void {
        // $ctx->request, $ctx->routeName(), $ctx->route, $ctx->package()
    });

    $register->onApi('auth.login', function (AppWatchContext $ctx): void {});

    $register->onPath('/manager/app/*', function (AppWatchContext $ctx): void {});

    $register->onController([AppViewController::class, 'run'], function (AppWatchContext $ctx): void {});

    $register->onModel(OrderModel::class, 'creating', function (AppWatchContext $ctx): void {
        // $ctx->model
    });

    // plugin: فقط وقتی host فعال است
    $register->onRoute('app.run', $handler, 'com_host_app');
};
```

| متد | زمان اجرا |
|-----|-----------|
| `onRoute` | نام route وب match شد (قبل از controller) |
| `onApi` | نام route API match شد |
| `onPath` | path درخواست (`*` = prefix) |
| `onController` | قبل از اجرای controller |
| `onAction` | named action match شد |
| `onModel` | رویداد Eloquent |
| `onTheme` | فعال شدن context یا نام پوشهٔ theme |

**Flow** برای middleware (block/redirect). **Watch** برای side effect (log، sync).

```php
$register->onTheme('panel', function (AppWatchContext $ctx): void {
    // $ctx->themeContext(), $ctx->themeName(), $ctx->themeStack()
});
```

route با permission:

```php
$register->route([
    'path' => '/panel',
    'action' => [PanelController::class, 'index'],
    'flow' => ['auth'],
    'permission' => 'app.panel.view',
]);
```

---

## Theme — context، ارث‌بری، و hook در boot

پوشه‌های theme در `apps/{package}/theme/{name}/` هستند. theme فعال را در **`app.php`** تنظیم کنید؛ برای runtime (دادهٔ global در view، عوض کردن context بر اساس route) از **`boot.php`** استفاده کنید.

### کلیدها در `app.php`

| کلید | کار |
|------|-----|
| `theme` | پوشهٔ theme فعال (مثلاً `'default'`) |
| `theme-context` / `theme-contexts` | چند theme برای یک اپ (site / panel / …) |
| `theme-extends` | ارث‌بری از theme دیگر |
| `path-theme` | مسیر سفار به‌جای `theme/` |
| `frontend` | پروفایل Vite، entry، manifest |

مثال چند context:

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site'  => ['theme' => 'site'],
    'panel' => ['theme' => 'panel'],
    'kids'  => ['theme' => 'kids', 'extends' => 'site'],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel', 'kids']),
),
```

اتصال context به route با Flow:

```php
// routes/web.php
get('/panel', [PanelController::class, 'index'], flows: ['auth', 'theme.panel']);
```

داخل `theme/{name}/`: `theme.php` (manifest + `extends`)، قالب Twig، `functions.php` (اختیاری)، `frontend.config.php`، `src/` / `dist/` برای Vite. child روی parent override می‌کند؛ parent بین‌اپ: `@com_base/default`.

**راهنماهای کامل:** [کانتکست تم](../basic/theme-contexts.md) · [مانیفست تم (`theme.php`)](../basic/theme-manifest.md) · [Views](../basic/views.md) · [Twig](../basic/templates.md) · [app.php](../start/app-manifest.md).

### از `boot.php`

از **`onTheme`** برای hook ساده استفاده کنید، یا **listen** / **watch** برای کنترل بیشتر:

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Component\Template\Theme\ThemeContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    function (): void {
        View::set('brand', config('brand.name'));
    },
);

$register->onPath('/panel/*', function (AppWatchContext $ctx): void {
    ThemeContext::activate('panel');
});
```

در controller هم می‌توانید `View::changeTheme('panel')`، `ThemeContext::activate('panel')` یا `within_theme('panel', fn () => View::render('pages/dashboard'))` بزنید.

| نیاز | راه |
|------|-----|
| یک theme ثابت | `'theme' => 'default'` در `app.php` |
| site + پنل جدا | `theme-contexts` + `theme_flow_aliases` روی route |
| extend روی base | `theme.php` → `'extends' => ['parent']` |
| متغیر global Twig | `View::set()` در boot یا controller |
| theme فقط برای بعضی routeها | Flow `theme.panel` یا watch/listen روی path |

build / cache:

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

---

## Service container (`bindings.php`)

با `container.enabled => true` در `app.php`، bindingها از `container.bindings` و `apps/{package}/bindings.php` merge می‌شوند:

```php
// bindings.php
return [
    OrderRepositoryInterface::class => OrderRepository::class,
];
```

اپ‌های جدید از `php pinoox app:create` stubهای `boot.php` و `bindings.php` می‌گیرند.

برای مراحل کامل pipeline بوت، مدل دو کانتینر، و تزریق سازنده کنترلر به [Kernel و pipeline بوت](./kernel.md) مراجعه کنید.

---

## Portal Event

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

در [ایمیل](./mail.md) از Event برای جدا کردن ارسال از کنترلر استفاده شده است.

**Flow** = قبل از کنترلر (middleware). **Event** = بعد از عمل (side effect).

---

## Helper

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot(?string $package = null): AppRegister
```

---

## cache boot

```php
'cache' => [
    'enabled' => true,
    'stores' => ['boot' => true, 'api' => true],
],
```

ساخت cache: `php pinoox cache:build {package}` (یا `.pinx install`) — [Pinker](./pinker.md).

---

## فایل‌های مرتبط

| فایل | نقش |
|------|-----|
| `boot.php` | ثبت programmatic |
| `bindings.php` | DI bindings |
| `schedule.php` | cron (فایل) |
| `routes/web.php` | route وب |
| `routes/api.php` | manifest API |

---

## مستندات مرتبط

- [Kernel و pipeline بوت](./kernel.md)
- [زمان‌بندی — Schedule](./schedule.md)
- [فلو — Flow](../basic/flows.md)
- [View](../basic/views.md)
- [Twig](../basic/templates.md)
- [روتر](../basic/routers.md)
- [ساختار پروژه](../start/structure.md)
- [manifest اپ](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)
