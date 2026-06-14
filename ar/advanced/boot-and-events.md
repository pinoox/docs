# ملف boot.php والأحداث (Events)

[← العودة إلى الفهرس](../README.md)

إلى جانب `routes/`، يمكنك تسجيل المسارات (Routes)، ونقاط نهاية API، والتدفقات (Flows)، والجداول الزمنية (Schedules)، والمستمعين (Listeners) في **`boot.php`** — وهو مفيد **للإضافات (Plugins)**، أو الوحدات الصغيرة، أو الربط مع تطبيق مضيف (مثل manager).

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
