# Pinx CLI (مشاريع التطبيق الواحد)

[← العودة إلى الفهرس](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** هي واجهة سطر الأوامر للمطورين لمشاريع Pinoox ذات **التطبيق الواحد** — توليد الهياكل والتشغيل والترحيل والبناء وشحن حزم `.pinx` دون لمس مدير متعدد التطبيقات.

مبنية على `pinoox/pincore` وقالب `pinoox/app`. جذر مشروعك **هو** التطبيق: ملف `app.php` واحد، وحزمة واحدة، وسير عمل واحد.

> لتثبيتات المنصة الكلاسيكية متعددة التطبيقات، استخدم [`php pinoox`](./cli-reference.md) بدلًا من ذلك.

---

## البداية السريعة

ثبّت Pinx مرة واحدة، وأنشئ تطبيقًا جديدًا، وشغّله:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # يقترح com_my_shop — أكّد أو عدّل في المعالج
cd my-shop
cp .env.example .env          # عيّن قيم DB_* إذا كنت تستخدم قاعدة بيانات
pinx setup                    # ترحيل المنصة + التطبيق، وتشغيل الـ seeders
pinx dev                      # http://127.0.0.1:8000
```

أضف مجلد `bin` العام الخاص بـ Composer إلى `PATH` لديك إذا لم يُعثر على `pinx`:

- Linux / macOS: ‏`~/.composer/vendor/bin` أو `~/.config/composer/vendor/bin`
- Windows: ‏`%APPDATA%\Composer\vendor\bin`

| الخطوة | ما تفعله |
|------|--------------|
| `composer global require` | يثبّت الأمر `pinx` على جهازك |
| `pinx new my-shop` | يولّد من `pinoox/app`؛ يقترح المعالج حزمة من ثلاثة مقاطع (مثل `com_my_shop`) |
| `.env` | قاعدة البيانات ومسارات المشروع — انسخه من `.env.example` |
| `pinx setup` | دفعة واحدة: ترحيلات المنصة ← ترحيلات التطبيق ← الـ seeders |
| `pinx dev` | خادم تطوير PHP؛ يشغّل Vite أيضًا عند ضبط حزمة واجهة أمامية |

تتبع أسماء الحزم الصيغة `com_{vendor}_{name}` — مثل `com_acme_shop` و `ir_yekdo_app`. هل أنت بالفعل داخل مجلد فارغ؟ استخدم `pinx init` بدلًا من `pinx new`.

**فحص اختياري قبل `setup`:** يعرض `pinx doctor` تقريرًا عن PHP والتخطيط والبيئة وقاعدة البيانات وجاهزية البناء.

---

## البديل: `composer create-project`

بدون تثبيت عام — يأتي القالب مع `bin/pinx` داخل المشروع:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## ما الذي يميز التطبيق الواحد

تُبقي تثبيتات Pinoox الكلاسيكية عدة تطبيقات ضمن `apps/` وتختار واحدًا وقت التشغيل. أما **التطبيق الواحد** فيبسّط ذلك:

- `app.php` في جذر المشروع يحمل هوية الحزمة وإعدادات pinx
- المجلدات `Controller/` و `Model/` و `routes/` و `theme/` تقع في الجذر — وليس داخل `apps/{package}/`
- `platform/` يحوي إعدادات التوجيه والمشغّل المحلية (مستثناة من بناء `.pinx`)
- Pinx يستهدف تطبيقك **أنت** دائمًا — لا منتقي حزم ولا واجهة مدير

```
my-shop/                    ← جذر المشروع = جذر التطبيق
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← طبقة الاستضافة التطويرية والنشر (محلي فقط)
├── bin/pinx                ← نقطة دخول CLI الخاصة بالمشروع
└── vendor/pinoox/pincore   ← الإطار
```

---

## خيارات التثبيت

| الموضع | الكيفية | متى تستخدمه |
|-------|-----|-------------|
| **عام (Global)** | `composer global require pinoox/pinx-cli` | موصى به — `pinx new` و `pinx init` من أي مكان |
| **لكل مشروع** | يُشحن كـ `bin/pinx` في `pinoox/app` | بعد `composer create-project` — لا حاجة لتثبيت عام |

```bash
pinx -v          # إصدار CLI (مثل pinx-cli 1.1.7)
pinx list        # نظرة عامة مجمّعة على الأوامر
pinx help setup  # تفاصيل أمر واحد
```

---

## سير العمل اليومي

```bash
pinx dev                    # خادم محلي (+ Vite عند ضبط app.php → frontend.stack)
pinx dev --open             # فتح المتصفح بعد البدء
pinx dev --no-frontend      # PHP فقط

pinx migrate                # تشغيل ترحيلات التطبيق (--platform يشغّل المنصة أولًا)
pinx migrate:st             # حالة الترحيلات
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # عرض الإجراءات المسماة (--validate, --json)
pinx test                   # تشغيل اختبارات التطبيق (Pest)
```

**الواجهة الأمامية** (عندما يستخدم `theme/` تقنية Vue/React + Vite):

```bash
pinx fe:info                # الحزمة، سكربتات npm، المسارات
pinx fe:i                   # npm install
pinx fe:d                   # خادم تطوير Vite
pinx fe:b                   # بناء الإنتاج
pinx fe:sc --stack=vue      # توليد ملفات البداية
```

**الاعتماديات:**

```bash
pinx deps:st                # حالة Composer + npm
pinx deps:i                 # تثبيت الكل
pinx deps:up                # تحديث الكل
```

**Pinker** (كاش البناء):

```bash
pinx pinker:st              # الكاش مقابل المصدر
pinx pinker:rb              # إعادة البناء
pinx pinker:df              # الفروقات
```

---

## الشحن إلى الإنتاج

ابنِ حزمة `.pinx` للتثبيت على منصة Pinoox كاملة (Manager ← Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # رفع الإصدار في app.php + البناء
pinx release --sign         # التوقيع عند ضبط المفتاح في app.php → pinx.sign
```

يطبّق `pinx build` إعدادات افتراضية معقولة (يستثني `vendor/` و `bin/` و `.env` و `platform/` وأدوات التطوير). تجاوزها في `app.php` فقط عند الحاجة:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

يشغّل doctor تشخيصًا منظمًا ويقترح أوامر إصلاح عند فشل شيء ما:

| المجموعة | الفحوصات |
|-------|--------|
| **المشروع** | `app.php`، هوية الحزمة، تخطيط `platform/` |
| **وقت التشغيل** | إصدار PHP (≥ 8.1)، الامتدادات، المسارات القابلة للكتابة |
| **الاعتماديات** | مجلد vendor الخاص بـ Composer، و Node/npm الاختياريان |
| **البيئة** | وجود `.env` والمتغيرات الأساسية |
| **قاعدة البيانات** | الاتصال (يمكن تخطيه بـ `--skip-db`) |
| **الواجهة الأمامية** | حزمة القالب و `package.json` (يمكن تخطيه بـ `--skip-frontend`) |
| **البناء** | جاهزية التصدير، الأيقونة، حقول الإصدار |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # تقرير مناسب لـ CI
pinx doctor --no-fixes      # إخفاء الأوامر المقترحة
```

---

## مرجع الأوامر

شغّل `pinx list` للحصول على نظرة عامة مقسمة. تظهر الاختصارات بين الأقواس.

### المشروع

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `new` | — | التوليد من `pinoox/app` (معالج أو رايات) |
| `init` | — | تهيئة المجلد الحالي (`--force` للاستبدال) |
| `setup` | — | قاعدة البيانات: ترحيل المنصة + التطبيق، ثم الـ seeders |
| `doctor` | `dr` | فحص الصحة — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | عرض البيانات الوصفية من `app.php` |

### التطوير

| الأمر | الوصف |
|---------|-------------|
| `dev` | خادم التطوير؛ Vite عندما يكون `frontend.stack` هو vue/react |

### قاعدة البيانات

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `migrate:run` | `migrate` | تشغيل ترحيلات التطبيق (`--platform` يشغّل المنصة أولًا) |
| `migrate:status` | `migrate:st` | حالة الترحيلات |
| `migrate:rollback` | `migrate:rb` | التراجع عن آخر دفعة (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | إنشاء ملف ترحيل |
| `migrate:platform` | `migrate:pl` | ترحيلات المنصة فقط |
| `seeder:run` | `seed` | تشغيل الـ seeders (الصنف عبر `-c`) |

### التصحيحات (Patches)

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `patch:run` | `patch` | تشغيل التصحيحات المعلّقة |
| `patch:status` | `patch:st` | حالة التصحيحات |
| `patch:rollback` | `patch:rb` | التراجع عن آخر دفعة تصحيحات |

### البناء والإصدار

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `build` | `bld` | بناء حزمة `.pinx` |
| `release` | `rel` | رفع الإصدار + البناء (`--bump`, `--sign`) |

### توليد الهياكل (Scaffolding)

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### المسارات (Routes)

| الأمر | الوصف |
|---------|-------------|
| `route:actions` / `routes` | عرض الإجراءات المسماة (`--validate`, `--json`) |

### الاعتماديات

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `deps:status` | `deps:st` | حالة Composer + npm |
| `deps:install` | `deps:i` | تثبيت الاعتماديات |
| `deps:update` | `deps:up` | تحديث الاعتماديات |

### الواجهة الأمامية

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | حزمة القالب وسكربتات npm |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | بناء الإنتاج |
| `fe:dev` | `fe:d` | خادم تطوير Vite |
| `fe:scaffold` | `fe:sc` | ملفات البداية (`--stack=vue\|react\|twig`) |

### الجدولة (Schedule)

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | عرض مهام cron من `schedule.php` |
| `schedule:run` | `sched:run` | تشغيل المهام المستحقة (`--dry-run`) |

### Pinion (الرفع القابل للاستئناف)

يُحوَّل إلى `php pinoox pinion:*` — إدارة جلسات الرفع المجزأ المؤقتة.

| الأمر | الوصف |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

مستندات: [بروتوكول Pinion](../advanced/pinion.md).

### Pinker

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | الكاش مقابل المصدر |
| `pinker:rebuild` | `pinker:rb` | إعادة بناء الكاش |
| `pinker:diff` | `pinker:df` | عرض الفروقات |
| `pinker:clear` | `pinker:cl` | مسح الكاش |
| `pinker:overrides` | `pinker:ov` | عرض التجاوزات (overrides) |

### الجودة والوثائق

| الأمر | الوصف |
|---------|-------------|
| `test` / `pest` | تشغيل اختبارات التطبيق (`--unit`, `--feature`) |
| `api:docs` | وثائق REST API |
| `graphql:docs` | وثائق مخطط GraphQL |

### أوامر عامة

| الأمر | الاختصارات | الوصف |
|---------|---------|-------------|
| `list` | — | نظرة عامة مجمّعة على الأوامر |
| `version` | `ver` | إصدار CLI |

---

## اكتشاف التطبيق

يصعد Pinx من مجلد العمل الحالي حتى يجد مشروع تطبيق واحد صالحًا:

1. وجود `app.php` يُرجع مصفوفة تحتوي مفتاح `package` غير فارغ
2. وجود `pinoox/pincore` ضمن متطلبات `composer.json`، أو وجود `vendor/pinoox/pincore`

يمكنك تجاوز الحزمة المكتشفة عبر متغيرات البيئة:

| المتغير | الغرض |
|----------|---------|
| `PINX_PACKAGE` | فرض الحزمة المستهدفة لـ CLI |
| `PINOOX_DEV_APP` | اسم بديل لـ `PINX_PACKAGE` |
| `PINX_DEV=1` | وضع التطوير (يضبطه pinx تلقائيًا عند التفويض إلى pincore) |

---

## المتطلبات

- **PHP** ≥ 8.1 مع الامتدادات التي يتطلبها `pinoox/pincore`
- **Composer** 2.x
- **Node.js** + npm — فقط عند استخدام واجهات Vite/Vue/React
- **قاعدة البيانات** — MySQL/MariaDB أو ما يضبطه ملف `.env` لديك (اختيارية للتطبيقات الثابتة أو Twig فقط)

---

## وثائق ذات صلة

- [تثبيت Pinoox](./installing-pinoox.md)
- [مرجع Pinoox CLI (متعدد التطبيقات)](./cli-reference.md)
- [تطبيقك الأول](./your-first-app.md)
- [ملف بيان app.php](./app-manifest.md)

---

[← العودة إلى الفهرس](../README.md)
