# زیر‌برنامه‌ها و مانت اپ (Sub-App)

[← بازگشت به فهرست](../README.md)

قابلیت **Sub-App** در پینوکس به شما این امکان را می‌دهد که یک اپلیکیشن مستقل را در زیر‌مسیر یک اپلیکیشن دیگر متصل (Mount) کرده و اجرا نمایید، بدون اینکه مسیرها، کنترلرها یا ساختار داخلی اپ مهمان دستخوش تغییر شود.

همچنین این قابلیت امکان تزریق **پیکربندی موقت در حافظه (Config Overlays)**، ارسال داده‌های کانتکست مشترک و اعمال محدودیت‌های امنیتی (مانند مجاز بودن فقط به عنوان زیر‌برنامه یا انحصار به میزبان‌های خاص) را فراهم می‌کند.

---

## موارد کاربرد

- **یکپارچه‌سازی ماژول‌ها:** سوار کردن یک ماژول مستقل (مانند وبلاگ، فروشگاه، تیکتینگ یا درگاه پرداخت) در زیرمسیر یک پرتال یا وب‌سایت اصلی.
- **پیش‌نمایش امن (Preview / Secret View):** باز کردن و اجرای اپلیکیشن‌ها در محیط یک اپلیکیشن ادمین (مشابه قابلیت مشاهده اپ‌ها در Pinoox Manager).
- **حالت‌های نمایشی و تست (Demo / Testing):** اجرای اپ با تنظیمات یا قالب متفاوت در لحظه، بدون تغییر فایل‌های کانفیگ روی دیسک.

---

## تعریف در روتر (Router)

برای مانت کردن یک اپلیکیشن به عنوان زیر‌برنامه، می‌توانید از متد `subApp` در فلوئنت روتینگ استفاده کنید:

```php
// routes/web.php
use function Pinoox\Router\subApp;
use Pinoox\Portal\Route;

// تعریف ساده
subApp('/pay', 'com_pinoox_payment');

// یا با استفاده از Route Portal و API زنجیره‌ای (Fluent)
Route::subApp('/shop', 'com_pinoox_shop')
    ->config([
        'theme' => 'minimal',
        'debug' => true,
    ])
    ->context([
        'embed_mode' => true,
        'portal_user_id' => 120,
    ])
    ->name('shop.embedded');
```

> **نکته:** زیر‌مسیرها به صورت خودکار پشتیبانی می‌شوند؛ برای مثال در سناریوی بالا، درخواست‌های `/shop/cart` یا `/shop/product/42` مستقیماً به روت‌های داخلی `com_pinoox_shop` ارجاع داده می‌شوند.

### متدهای `SubAppRouteBuilder`

| متد | توضیح |
|-----|-------|
| `path(string $path)` | تعیین مسیر دلخواه در فایل‌سیستم برای زیر‌برنامه (ثبت خودکار در `AppEngine`). |
| `appPath(string $path)` | نام مستعار برای `path()` جهت تعیین مسیر پوشه برنامه. |
| `config(array $overrides)` | بازنویسی موقت کلیدهای `app.php` اپ مهمان (تنها در طول پردازش همین درخواست). |
| `context(array $data)` | ارسال داده‌های کمکی و اختیاری از اپ میزبان به اپ مهمان. |
| `name(string $name)` | تعیین نام برای روت پایه زیر‌برنامه. |
| `flows(array $flows)` | اعمال میان‌افزارها و Flowها پیش از ورود به زیربرنامه. |
| `methods(array\|string $methods)` | محدود کردن متدهای HTTP مجاز (پیش‌فرض تمام متدهاست). |

---

## تشخیص خودکار مسیر اپلیکیشن از طریق `AppEngine`

پینوکس برای مدیریت موقعیت و دسترسی به تمام منابع اپلیکیشن‌ها (روت‌ها، کنترلرها، کانفیگ، ترجمه‌ها، قالب‌ها) از **`AppEngine`** استفاده می‌کند. در زیر‌برنامه‌ها (Sub-Apps)، تشخیص مسیر به شکل کاملاً خودکار و هوشمند انجام می‌شود:

1. **پوشه استاندارد `apps/`:** اگر اپلیکیشن در مسیر پیش‌فرض `apps/{package}` قرار داشته باشد، بدون نیاز به هیچ تنظیم دستی توسط `AppEngine` شناسایی و اجرا می‌شود.
2. **پوشه زیر‌برنامه‌های اپ میزبان (`sub_apps/`):** اگر زیر‌برنامه در پوشه‌های داخلی اپ میزبان (مانند `apps/{host}/sub_apps/{guest}` یا `apps/{host}/apps/{guest}`) قرار داشته باشد، پینوکس آن را خودکار ردیابی و در `AppEngine` رجیستر می‌کند.
3. **رجیستری سرورهای توسعه فعال (`AppDevRegistry`):** اگر اپلیکیشن مهمان روی یک سرور توسعه (`pinx dev`) به صورت مستقل در حال اجرا باشد، `AppEngine` آن را به صورت خودکار و موقت در حافظه شناسایی و مانت می‌کند (بدون نیاز به تنظیم دستی در `apps.config.php`).
4. **مسیر دلخواه یا سفارشی روی دیسک:** می‌توانید یک اپلیکیشن را از هر مسیر دلخواهی مانت کنید:

```php
// روش اول: با متد فلوئنت path()
Route::subApp('/payment', 'com_payment')
    ->path(path('sub_apps/payment'));

// روش دوم: ارسال مستقیم مسیر پوشه به جای نام پکیج
Route::subApp('/chat', path('modules/live_chat'));

// روش سوم: از طریق آرایه تنظیمات options
Route::subApp('/blog', 'com_blog', [
    'path' => '/var/custom_apps/blog',
]);
```

برای دریافت مسیر فیزیکی زیر‌برنامه در فایل‌های PHP یا قالب‌های Twig:
- در PHP: تابع `sub_app_path()` یا متد `SubApp::path('package_name', 'sub/path')`
- در قالب Twig: تگ `{{ sub_app_path() }}` یا `{{ sub_app_path('package_name') }}`

---

## اورلی داینامیک تنظیمات (Dynamic Config Overlays)

هنگامی که یک اپلیکیشن به صورت Sub-App لود می‌شود، می‌توانید بدون دستکاری فایل‌های فیزیکی دیسک، تنظیمات `app.php` آن را موقتاً تغییر دهید.

این تنظیمات از طریق کلاس `LayeredConfig` به صورت لایه‌ای روی کانفیگ اصلی قرار می‌گیرند:
- متدهای `get()` و `all()` ابتدا مقدار لایه موقت و سپس کانفیگ پایه را برمی‌گردانند.
- هرگونه `set()` موقتی بوده و متد `save()` مسدود است تا فایل‌های روی سرور هرگز تغییر نکنند.
- بلافاصله پس از اتمام درخواست، پشته‌ی تنظیمات به طور خودکار به حالت اولیه برمی‌گردد.

```php
Route::subApp('/demo-blog', 'com_pinoox_blog')
    ->config([
        'theme' => 'dark-demo',
        'comments_enabled' => false,
    ]);
```

---

## محدودیت‌ها و امنیت در `app.php`

اپلیکیشن مهمان می‌تواند نحوه و شرایط اجرای خود به عنوان زیر‌برنامه را در فایل `app.php` کنترل کند:

```php
// apps/com_pinoox_payment/app.php
return [
    'package' => 'com_pinoox_payment',
    
    // ۱. جلوگیری از اجرای مستقل:
    // این اپ نمی‌تواند مستقیماً روی دامنه یا روت‌های سطح بالای پلتفرم فعال شود.
    'subapp_only' => true,

    // ۲. وایت‌لیست اپلیکیشن‌های میزبان مجاز:
    // تنها این اپ‌ها اجازه مانت کردن این زیربرنامه را دارند.
    'allowed_hosts' => [
        'com_pinoox_manager',
        'com_pinoox_portal',
    ],
];
```

> در صورتی که اپلیکیشنی که در لیست `allowed_hosts` نیست اقدام به اجرای زیربرنامه کند، پلتفرم خطای امنیتی `403 Access Denied` صادر می‌کند.

---

## توابع کمکی و تشخیص وضعیت (Helpers)

اپ مهمان می‌تواند بررسی کند که آیا در قالب یک زیربرنامه باز شده است یا خیر، و کانتکست ارسالی را دریافت کند:

### در PHP:

```php
use Pinoox\Portal\SubApp;
use Pinoox\Portal\App\App;

// بررسی آیا اپلیکیشن به صورت زیربرنامه در حال اجراست؟
if (is_sub_app()) {
    // دریافت شناسه پکیج میزبان
    $host = sub_app_parent(); // یا sub_app_host()
    
    // دریافت کانتکست ارسال‌شده
    $isEmbed = sub_app_context('embed_mode', false);
}

// بررسی میزبانی توسط یک پکیج خاص
if (is_sub_app_of('com_pinoox_manager')) {
    // کدهای ویژه در صورت مشاهده داخل پنل منیجر
}
```

همچنین متدهای معادل از طریق پورتال `SubApp` و فاساد `App` در دسترس است:
- `SubApp::isSubApp()` یا `App::isSubApp()`
- `SubApp::parent()` یا `App::parent()`
- `SubApp::isSubAppOf('package_name')` یا `App::isSubAppOf(...)`
- `SubApp::context(?string $key = null, mixed $default = null)`

### در تمپلیت‌های Twig:

تمامی این متدها به عنوان توابع سراسری در موتور قالب Twig رجیستر شده‌اند:

```twig
{% if is_sub_app() %}
    <div class="subapp-banner">
        در حال نمایش در میزبان: {{ sub_app_parent() }}
    </div>
{% endif %}

{% if is_sub_app_of('com_pinoox_manager') %}
    {# مخفی کردن سایدبار عمومی اپ هنگام پیش‌نمایش در منیجر #}
{% else %}
    {% include 'header.html' %}
{% endif %}

{% if sub_app_context('embed_mode') %}
    <style>body { background: transparent; }</style>
{% endif %}
```

---

## اجرای دستی از طریق کنترلر (`SubApp::run`)

اگر نیاز دارید یک زیر‌برنامه را خارج از روتینگ مستقیم (مثلاً درون یک اکشن خاص کنترلر) فراخوانی کنید:

```php
namespace App\com_pinoox_manager\Controller;

use Pinoox\Component\Http\Request;
use Pinoox\Portal\SubApp;

class AppViewController
{
    public function open(string $packageName, Request $request)
    {
        $mountPath = 'app/' . $packageName;

        return SubApp::run(
            guestPackage: $packageName,
            mountPath: $mountPath,
            request: $request,
            config: ['debug' => true],
            context: ['admin_token' => 'xyz']
        );
    }
}
```
