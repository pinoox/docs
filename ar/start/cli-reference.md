# مرجع Pinoox CLI

[← العودة إلى الفهرس](../README.md)

شغّل كل الأوامر من **جذر المشروع**:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

عندما تكون الحزمة (package) مطلوبة وتم حذفها، يعرض Pinoox منتقيًا تفاعليًا.

> لمشاريع **التطبيق الواحد**، استخدم [Pinx CLI](./pinx-cli.md) المستقل (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## الاختصارات الشائعة

| الاختصار | الأمر |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## التطبيقات

| الأمر | الغرض |
|---------|---------|
| `app:create {package}` | توليد هيكل تطبيق (`--simple`, `--stack`, `--profile`) |
| `app:list` | عرض قائمة التطبيقات |
| `app:delete` | حذف تطبيق |
| `app:router set /path {package}` | ربط الـ URL |
| `app:domain` | ربط المضيف (Host) ← التطبيق |
| `app:resolve` | تتبّع التطبيق النشط (debug) |

---

## توليد الهياكل (Scaffolding)

| الأمر | الناتج |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | صنف FormRequest |
| `seeder:create` | `database/seed/` |
| `test:create` | ملف Pest |
| `theme:frontend` | أدوات الواجهة الأمامية (Vue/React/Twig) |

---

## قاعدة البيانات

| الأمر | الغرض |
|---------|---------|
| `migrate {package}` | تشغيل الترحيلات (التطبيق، `platform`، `pincore`) |
| `migrate:create` | ملف ترحيل جديد |
| `migrate:status` / `migrate:rollback` | الحالة / التراجع |
| `seeder:run` | تشغيل الـ seeders |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [التصحيحات (Patches)](../database/patches.md) |
| `query` | SQL خام (للتتبع) |

---

## الكاش و Pinker

| الأمر | الغرض |
|---------|---------|
| `cache:build` / `cache:clear` | كاش وقت التشغيل |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | إعادة تعيين Pinker + الإعدادات |

---

## الجدولة (Schedule)

| الأمر | الغرض |
|---------|---------|
| `schedule:list` | عرض مهام cron |
| `schedule:run` | تشغيل المهام المستحقة |

راجع [الجدولة](../advanced/schedule.md).

---

## الموجّه (Router)

| الأمر | الغرض |
|---------|---------|
| `route:actions {package}` | عرض الإجراءات المسماة (Named Actions) |

---

## حزم Pinx

| الأمر | الغرض |
|---------|---------|
| `pinx:build` | بناء حزمة `.pinx` |
| `pinx:install` | تثبيت الحزمة |
| `pinx:info` | البيانات الوصفية |
| `wizard:list` / `wizard:install` | معالج التثبيت |

---

## التطوير

| الأمر | الغرض |
|---------|---------|
| `test` | اختبارات Pest |
| `serve` | خادم التطوير المدمج |
| `log:view` / `log:clear` | السجلات |
| `deps` | Composer/npm عبر التطبيقات |
| `version` / `mode:show` | الإصدار / وضع التشغيل |

---

## وسيط الحزمة (Package argument)

| القيمة | المعنى |
|-------|---------|
| `com_my_shop` | تطبيق محدد |
| `platform` | ترحيلات/تصحيحات/seeders المنصة |
| `pincore` | نواة الإطار |
| `all` | كل التطبيقات (cache/pinker) |

---

## وثائق ذات صلة

- [تطبيقك الأول](./your-first-app.md)
- [الترحيلات (Migrations)](../database/migrations.md)
- [التصحيحات (Patches)](../database/patches.md)

---

[← العودة إلى الفهرس](../README.md)
