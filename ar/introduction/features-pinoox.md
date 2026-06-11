# ميزات Pinoox

[← العودة إلى الفهرس](../README.md)

صُمّم Pinoox 3.x لمنظومة PHP معيارية: عدة تطبيقات مستقلة على نواة مشتركة واحدة، وتوليد الهياكل عبر CLI، وأدوات مدمجة لـ HTTP وقاعدة البيانات والقوالب (Themes) والمصادقة.

---

## معمارية HMVC وتطبيقات مستقلة

كل تطبيق ضمن `apps/{package}/` له بنية MVC كاملة:

| الطبقة | مسار نموذجي |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (middleware) | `Flow/AuthFlow.php` |

إضافة تطبيق أو تعطيله لا يؤثر على بقية التطبيقات.

---

## CLI والتطوير السريع

من جذر المشروع:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

تولّد واجهة CLI تخطيط المجلدات القياسي وملف `app.php` وملفات المسارات (Routes) الأولية.

---

## التوجيه (Routing) والإجراءات المسماة (Named Actions)

تُفصل مسارات URL عن المعالجات المنطقية:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

هذا النمط يجعل إعادة الهيكلة والاختبار أسهل.

---

## Flow (الوسيط / middleware)

قبل وصول الطلب (Request) إلى المتحكم (Controller)، تعمل الـ Flows — للمصادقة والتفويض والتسجيل وغير ذلك:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

سجّل أسماء Flow المستعارة في `app.php`.

---

## العروض (Views) والقوالب (Themes)

- قوالب Twig في `theme/{themeName}/`
- العرض عبر **`View::render()`**
- دعم SPA باستخدام Vite داخل القالب (Vue/React)

---

## قاعدة البيانات و Eloquent

- Query Builder و Eloquent عبر بوابة (Portal) باسم `DB`
- الترحيلات (Migrations) والـ seeders في `database/migrations/` لكل تطبيق
- بادئة الجداول مبنية على اسم الحزمة (مثل `com_acme_blog_posts`)

---

## استجابات API و JSON

قم بتوريث **`ApiController`** واستخدم الغلاف القياسي:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## التدويل (Internationalization)

ملفات الترجمة في `lang/{locale}/*.lang.php` — مناسبة للتطبيقات متعددة اللغات.

---

## وثائق ذات صلة

- [ما هو Pinoox؟](./what-is-pinoox.md)
- [تثبيت Pinoox](../start/installing-pinoox.md)
- [الموجّه (Router)](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← العودة إلى الفهرس](../README.md)
