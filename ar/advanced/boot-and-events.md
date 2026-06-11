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
'extends' => ['com_pinoox_manager'],
```

تُشغَّل الإضافة الخاصة بك فقط عند تشغيل المضيف (أخف من النمط العام).

---

## ملف boot.php أساسي

```php
<?php

use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->apiRoute([
        'method' => 'GET',
        'uri' => '/health',
        'action' => fn () => response()->json(['ok' => true]),
        'name' => 'health',
    ]);

    $register->when('com_pinoox_manager', function (AppRegister $host) {
        $host->apiRoute([
            'method' => 'GET',
            'uri' => '/acme/status',
            'action' => fn () => response()->json(['status' => 'ok']),
            'name' => 'acme.status',
            'flow' => ['manager.auth'],
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
