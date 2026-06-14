# ملف boot.php والأحداث (Events)

[← العودة إلى الفهرس](../README.md)

إلى جانب `routes/`، يمكنك تسجيل المسارات (Routes)، ونقاط نهاية API، والتدفقات (Flows)، والجداول الزمنية (Schedules)، والمستمعين (Listeners) في **`boot.php`** — وهو مفيد **للإضافات (Plugins)**، أو الوحدات الصغيرة، أو الربط مع تطبيق مضيف (مثل manager).

يمكن لكل تطبيق أن يوفّر `apps/{package}/boot.php`. يُرجع الملف closure يستقبل `AppRegister` ويُنفَّذ **قبل** معالجة الطلب.

---

## دورة الإقلاع (Lifecycle)

```
HTTP request
  → BootPipeline (composer → loader → boot.global → app.boot → container → …)
  → AppBootstrap::ensure($package)
  → include boot.php → callable($register)
  → commit registries → integrate (flows, listeners, events)
  → router / API loaders apply registered entries
```

### مراحل pipeline

| المرحلة | الغرض |
|---------|--------|
| `boot.global` | boot التطبيقات ذات `boot-global => true` في كل طلب |
| `app.boot` | boot التطبيق النشط (+ extenders عبر `extends`) |

### أحداث boot

| الاسم | متى |
|-------|-----|
| `app.booting` / `app.booting.{package}` | قبل commit |
| `app.booted` / `app.booted.{package}` | بعد integrate |
| `app.routes` / `app.routes.{package}` | عند تطبيق مسارات الويب |
| `app.api` / `app.api.{package}` | عند بناء registry الـ API |

الاستماع من `boot.php`:

```php
use Pinoox\Component\AppEvent\AppEventNames;

$register->listen(
    AppEventNames::package(AppEventNames::BOOTED, $register->package()),
    $listener,
);
```

### أحداث طلب HTTP (النواة)

يُطلَقها الإطار تلقائياً في كل طلب HTTP (`AppCoreEventSubscriber`):

| الاسم | متى | variant الحزمة | قناة مسماة |
|-------|-----|----------------|------------|
| `app.route.matched` | بعد تطابق المسار | `app.route.matched.{package}` | `app.route.{routeName}` أو `app.api.{routeName}` |
| `app.controller` | قبل تشغيل المتحكم | `app.controller.{package}` | `app.controller.{Class}.{method}` |
| `app.response` | قبل إرسال الاستجابة | `app.response.{package}` | — |
| `app.exception` | عند استثناء غير معالج | `app.exception.{package}` | — |
| `app.terminate` | بعد إرسال الاستجابة | `app.terminate.{package}` | — |

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

استخدم **watches** (`onRoute`, `onApi`, …) للخطافات البسيطة؛ و**listen** على أحداث النواة للتحكم الكامل أو إضافات cross-app.

---

## أنماط التطبيق الثلاثة

| النمط | الإعداد | السلوك |
|------|--------|----------|
| **Route فقط** | `router.routes` فقط | يعمل عندما يكون عنوان URL الخاص بالتطبيق نشطاً |
| **Boot عام (Global)** | `boot-global => true` | يتم تشغيله مع كل طلب HTTP |
| **Boot + Route** | `boot.php` + المسارات | الهيكل الافتراضي |

إضافة (Plugin) على تطبيق مضيف:

```php
'extends' => ['com_host_app'],
```

تُشغَّل الإضافة الخاصة بك فقط عند تشغيل المضيف (أخف من النمط العام).

---

## مفاتيح `app.php` الخاصة بالـ boot

تتحكم هذه المفاتيح في `apps/{package}/app.php` في **ما إذا** يُنفَّذ `boot.php`، **ومتى**، وهل تُخزَّن نتائجه في الذاكرة المؤقتة. إنها تضبط pipeline الإقلاع — ولا تستبدل `boot.php`.

### ملف boot (`boot`)

| القيمة | الافتراضي | ماذا يحدث |
|--------|-----------|-----------|
| `true` | نعم | تشغيل `boot.php` عند boot التطبيق |
| `false` | | بدون boot — مسارات فقط |
| `'path/custom.php'` | | ملف آخر نسبةً لجذر التطبيق |

يجب أن **يُرجع الملف callable**: `fn (AppRegister $register) => …`. إن غاب الملف مع `true`، يُتخطَّى boot بصمت.

### إضافة عامة (`boot-global`)

| القيمة | الافتراضي | ماذا يحدث |
|--------|-----------|-----------|
| `false` | نعم | boot فقط عندما يكون التطبيق نشطاً |
| `true` | | boot في **كل طلب HTTP** |

### إضافة على مضيف (`extends`)

| القيمة | الافتراضي | ماذا يحدث |
|--------|-----------|-----------|
| `[]` | نعم | تطبيق عادي |
| `['com_host_app']` | | boot **قبل** المضيف عند تفعيله |

### تسجيل إضافي (`startup`)

callable اختياري في `app.php`، **بعد** `boot.php`، بنفس واجهة `AppRegister`.

### ذاكرة boot (`cache`)

اختياري: يجب أن يكون `cache.enabled` = `true`. بعد النشر: `php pinoox cache:build {package}`.

### اختيار سريع

| الهدف | الإعداد |
|-------|---------|
| تطبيق عادي | `'boot' => true` |
| مسارات فقط | `'boot' => false` |
| إضافة عامة | `'boot-global' => true` |
| إضافة على مضيف | `'extends' => ['com_host_app']` |

---

## ملف boot.php أساسي

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;
use Pinoox\Component\Http\Api\ApiResponse;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => ApiResponse::success(['status' => 'ok']),
        'name' => 'health',
    ]);

    $register->when('com_host_app', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => ApiResponse::success(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['host.auth'],
        ]);
    });
};
```

---

## AppRegister — الدوال الشائعة

| الدالة | الغرض |
|--------|---------|
| `web(callable)` | تسجيل المسارات عبر المُنشئ (Builder) |
| `route([...])` | مسار ويب واحد |
| `api([manifest])` | بيان API كامل |
| `apiRoute([...])` | نقطة نهاية API واحدة |
| `action('name', handler)` | إجراء (Action) مُسمّى |
| `flowAlias(['auth' => AuthFlow::class])` | اسم بديل لتدفق (Flow alias) |
| `schedule(callable)` | مهمة مجدولة |
| `listen('event', listener)` | مستمع حدث (Event listener) |
| `subscribe(SubscriberClass::class)` | مشترك (Subscriber) من Symfony |
| `when('com_host', fn)` | ربط عند تشغيل تطبيق آخر |

---

## Theme — السياقات والوراثة وخطافات boot

مجلدات theme في `apps/{package}/theme/{name}/`. اضبط theme النشط في **`app.php`**؛ استخدم **`boot.php`** للخطافات وقت التشغيل (بيانات view عامة، تبديل context حسب route).

### مفاتيح `app.php`

| المفتاح | الغرض |
|---------|--------|
| `theme` | مجلد theme النشط |
| `theme-context` / `theme-contexts` | عدة themes (site / panel / …) |
| `theme-extends` | الوراثة من theme آخر |
| `path-theme` | مسار مخصص بدل `theme/` |
| `frontend` | Vite: profile، entry، manifest |

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

على routes: `flows: ['auth', 'theme.panel']`. داخل `theme/{name}/`: `theme.php`، Twig، `functions.php`، `frontend.config.php`، `src/` / `dist/`.

راجع [Views](../basic/views.md)، [Twig](../basic/templates.md)، [app.php](../start/app-manifest.md).

### من `boot.php`

استخدم **`onTheme`** أو **listen** / **watch**:

```php
use Pinoox\Component\AppEvent\AppWatchContext;
use Pinoox\Portal\View;

$register->onTheme('panel', function (AppWatchContext $ctx): void {
    View::set('layout', 'compact');
});
```

في Controller: `View::changeTheme('panel')`، `ThemeContext::activate('panel')`، `within_theme(...)`.

```bash
php pinoox theme:frontend build {package}
php pinoox cache:build {package} --only=twig
```

---

## بوابة الأحداث (Event portal)

```php
use Pinoox\Portal\Event;

Event::dispatch($event, OrderPlaced::NAME);
Event::listen(OrderPlaced::NAME, SendOrderEmail::class);
```

راجع [البريد الإلكتروني](./mail.md) لفصل إرسال البريد عن المتحكمات (Controllers).

**التدفق (Flow)** = قبل المتحكم (Middleware). **الحدث (Event)** = بعد إجراء ما (تأثيرات جانبية).

---

## الدوال المساعدة (Helpers)

```php
use Pinoox\Portal\AppBoot;

AppBoot::ensure();
AppBoot::booted('com_acme');
app_boot();
```

---

## ذاكرة التخزين المؤقت للإقلاع (Boot cache)

الإعداد `'boot' => true` تحت `cache.stores` في `app.php` يخزّن الإقلاع عبر Pinker — راجع [Pinker](./pinker.md).

---

## وثائق ذات صلة

- [الجدولة (Schedule)](./schedule.md)
- [التدفقات (Flows)](../basic/flows.md)
- [الموجّهات (Routers)](../basic/routers.md)
- [هيكل المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)
