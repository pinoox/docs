# الدوال المساعدة العامة (Global Helpers)

[← العودة إلى الفهرس](../README.md)

يحمّل Pinoox 3.x الدوال المساعدة العامة من `pincore/functions/`. لتطوير التطبيقات اليومي تكفي هذه الدوال المساعدة (إضافة إلى البوابات Portals) — لا تنشئ مكوّنات النواة (Core Components) مباشرةً.

---

## الدوال المساعدة الرئيسية

| الدالة المساعدة | الغرض | مثال |
|--------|---------|---------|
| `render()` | HTML كنص (String) | `$html = render('email', $data);` |
| `response()` | استجابة HTTP | `return response()->json($data);` |
| `redirect()` | إعادة توجيه | `return redirect(url('login'));` |
| `url()` | رابط التطبيق/الموقع | `url('products')` |
| `path()` | مسار الملف على القرص | `path('storage/logs/app.log')` |
| `assets()` | رابط ملف القالب (Theme) | `assets('dist/app.css')` |
| `config()` | قراءة/كتابة الإعدادات | `config('app.name')` |
| `t()` | ترجمة (إرجاع القيمة) | `t('welcome.title')` |
| `lang()` | ترجمة (طباعة مباشرة) | `lang('welcome.title')` |
| `app()` | التطبيق النشط | `app()->get('package')` |
| `auth()` | المستخدم المسجّل دخوله | `auth()` → `Auth::user()` |
| `user()` | حقل من بيانات المستخدم | `user('email')` |
| `isLogin()` | حالة تسجيل الدخول | `if (isLogin()) { … }` |
| `session()` | الجلسة (Session) | `session('token')` |
| `runtime()` | نواة HTTP النشطة | `runtime()->getRequest()` |
| `_env()` | متغير البيئة | `_env('APP_DEBUG', false)` |
| `alias()` | اسم بديل لتدفق/صنف | `alias('auth')` |

لإخراج HTML في المتحكمات (Controllers) استخدم **`View::render()`** (كما في تطبيقات النظام). الدالة `view()` موجودة لكن يُفضَّل استخدام البوابة (Portal) في المتحكمات.

---

## الطلب (Request) — الحقن أو `runtime()`

لا توجد دالة مساعدة عامة `request()` في pincore. في المتحكمات والمكوّنات استخدم الحقن عبر تلميح النوع (Type-hint injection):

```php
use Pinoox\Component\Http\Request;

public function save(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    $email = $request->requestOne('email');
    $all = $request->all();
}
```

داخل تدفق (Flow) أو في أي مكان لا يسمح فيه توقيع الدالة بالحقن:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## المصادقة (Auth) — `auth()` و `user()` والتدفق (Flow)

```php
// المستخدم الحالي (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) مماثلة تماماً لـ user($key)
$email = auth('email');

// حماية المسارات باستخدام اسم بديل لتدفق (Flow alias)
// app.php → 'alias' => ['auth' => AuthFlow::class]
// المسارات → ->flows(['auth']) أو مجموعة (group) مع flows
```

---

## العرض (View) والاستجابة (Response)

```php
use Pinoox\Portal\View;

return View::render('pages/list', ['items' => $items]);

return response()->json(['ok' => true]);

return redirect(url('dashboard'));
```

---

## الإعدادات (Config)

```php
$enabled = config('payment.enabled', false);

config('payment')->set('enabled', true)->save();
```

---

## اللغة (Lang)

```php
$label = t('product.title');
// في Twig: {{ t('product.title') }}
```

---

## الروابط (URL) والمسارات (Path)

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## دوال مساعدة مخصّصة للتطبيق

في `app.php`:

```php
'loader' => [
    '@func' => 'func.php',
],
```

```php
// apps/com_acme_shop/func.php
function format_price(float $amount): string
{
    return '$' . number_format($amount, 2);
}
```

---

## دوال Twig المساعدة (في القوالب)

إضافةً إلى دوال PHP المساعدة، تتوفر هذه الدوال في Twig:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## نصائح

- استخدم `View::render()` في المتحكمات لإخراج HTML؛ ودوال مساعدة مثل `url()` و `t()` و `config()` للمهام اليومية
- تعمل الدوال المساعدة فقط بعد إقلاع (Bootstrap) نظام Pinoox — لا تحمّلها في سكربتات PHP خام خارج `index.php` / `pinoox`
- للمنطق المعقّد، يُفضَّل استخدام `Component/` + Portal بدلاً من دوال مساعدة مخصّصة

---

## وثائق ذات صلة

- [البوابة (Portal)](../basic/portal.md)
- [الروابط (URL)](../basic/url.md)
- [المسارات (Path)](../basic/path.md)
- [اللغة](../basic/language.md)
- [الخدمات (Services)](./services.md)

---

[← العودة إلى الفهرس](../README.md)
