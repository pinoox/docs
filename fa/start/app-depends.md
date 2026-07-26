# وابستگی اپ‌ها (App dependencies)

[← بازگشت به فهرست](../README.md)

اپ‌ها می‌توانند **وابستگی** به اپ‌های نصب‌شده دیگر اعلام کنند. پینوکس وابستگی‌های الزامی را هنگام نصب `.pinx` اعتبارسنجی می‌کند و یک API استاندارد برای استفاده از config، lang، مسیر، action و کلاس‌های PHP اپ دیگر فراهم می‌کند.

تفاوت با سازوکارهای مشابه:

| سازوکار | کاربرد |
|---------|--------|
| `extends` | ترتیب boot افزونه — افزونه به اپ میزبان قلاب می‌شود |
| `depends` | الزام سخت/نرم به وجود اپ دیگر (و نسخه حداقلی اختیاری) |
| `use_app()` | دسترسی زمان اجرا به منابع اپ دیگر |

---

## اعلام وابستگی

در `app.php`:

```php
return [
    'package' => 'com_my_addon',

    // لیست ساده — اپ باید وجود داشته باشد
    'depends' => [
        'com_pinoox_manager',
    ],

    // نگاشت با محدودیت نسخه (version-code)
    'depends' => [
        'com_base_shop' => '>=2',
        'com_pinoox_manager' => '*',
    ],

    // وابستگی اختیاری — نصب بدون آن موفق است؛ در runtime از when() استفاده کنید
    'depends' => [
        'com_base_shop' => '>=2',
        'com_reviews' => ['optional' => true, 'min_code' => 1],
    ],
];
```

### قالب محدودیت‌ها

| شکل | معنی |
|-----|------|
| `'com_app'` (آیتم لیست) | اپ باید نصب باشد |
| `'com_app' => '*'` | همان |
| `'com_app' => '>=2'` | `version-code` اپ نصب‌شده باید ≥ ۲ باشد |
| `'com_app' => 2` | خلاصه عدد برای `>=2` |
| `'com_app' => ['optional' => true]` | وابستگی نرم |

---

## build و نصب Pinx

با `pinx:build` وابستگی‌ها در `manifest.json` کپی می‌شوند:

```json
{
  "package": "com_my_addon",
  "depends": {
    "com_base_shop": ">=2",
    "com_reviews": { "optional": true }
  }
}
```

هنگام `pinx:install`، بعد از چک `minpin`:

1. **مرحله `depends`** — هر اپ الزامی باید با نسخه مناسب وجود داشته باشد
2. اگر وابستگی نباشد یا قدیمی باشد، نصب با خطای واضح متوقف می‌شود

خروجی CLI:

```bash
php pinoox pinx:info export/com_my_addon.pinx   # ردیف‌های Depends
php pinoox pinx:build com_my_addon              # Depends در خلاصه
php pinoox pinx:install com_my_addon.pinx       # اعتبارسنجی قبل از extract
```

وابستگی‌های اختیاری در مانیفست هستند ولی **نصب را مسدود نمی‌کنند**.

---

## API زمان اجرا — `use_app()`

دسترسی به منابع اپ دیگر وقتی نصب است:

```php
use function Pinoox\use_app;

$shop = use_app('com_base_shop');

if ($shop->exists()) {
    $code = $shop->versionCode();
    $path = $shop->path('theme/default');
}
```

معادل Portal: `UseApp::use('com_base_shop')`.

### متدها

| متد | توضیح |
|-----|--------|
| `exists()` | آیا اپ نصب است |
| `stable()` | نصب و فعال است |
| `versionCode()` / `versionName()` | از `app.php` نصب‌شده |
| `config('database.host')` | کلید فایل config (`config/{name}.config.php`) |
| `lang('welcome.title')` | ترجمه از اپ وابستگی |
| `path('Controller/HomeController.php')` | مسیر مطلق فایل |
| `class('Model.OrderModel')` | FQCN: `App\com_base_shop\Model\OrderModel` |
| `hasAction('home')` | آیا action نام‌دار ثبت شده |
| `actionUrl('home')` | URL عمومی action اپ وابستگی |
| `when($callback, $default)` | فقط اگر اپ وجود دارد callback را اجرا کن |
| `meeting($callback)` | موقتاً context اپ فعال را عوض کن |

### مثال‌ها

**Config از اپ دیگر**

```php
$prefix = use_app('com_base_shop')->config('database.default', 'shop_');
```

**Lang از اپ دیگر**

```php
$label = use_app('com_pinoox_manager')->lang('user.profile');
```

**کنترلر اپ دیگر در routes**

```php
use App\com_base_shop\Controller\CatalogController;

get(
    path: '/catalog',
    action: [CatalogController::class, 'index'],
);
```

یا داخل closure با meeting:

```php
get(path: '/catalog', action: function () {
    return use_app('com_base_shop')->meeting(
        fn () => (new CatalogController())->index()
    );
});
```

**URL اکشن بین‌اپی**

```php
url()->action('@com_base_shop/home');
use_app('com_base_shop')->actionUrl('home');
```

**قابلیت اختیاری وقتی وابستگی هست**

```php
use_app('com_reviews')->when(function ($reviews) {
    return $reviews->config('reviews.enabled', false);
}, default: false);
```

**هلپرهای سراسری**

```php
app_resource('@com_base_shop:lang.menu.shop');
app_dep_satisfied(['com_base_shop' => '>=2']); // bool
```

---

## نحو ارجاع

ارجاع‌های بین‌اپی همان سبک `@package/...` تم‌ها را دارند:

| نوع | نحو | مثال |
|-----|-----|------|
| Action (URL) | `@pkg/action` | `@com_shop/home` |
| Config | `@pkg:config.{file}.{key}` | `@com_shop:config.database.host` |
| Lang | `@pkg:lang.{key}` | `@com_shop:lang.welcome.title` |
| Path | `@pkg:path.{relative}` | `@com_shop:path.theme/default` |
| Class | `@pkg:class.{Short}` | `@com_shop:class.Model.Order` |
| Theme | `@pkg/theme` | `@com_shop/spark` (ThemeReference) |
| Filesystem | `pkg:relative/path` | `com_shop:theme/default` (Path::get) |

پارس صریح:

```php
UseApp::parse('@com_shop:lang.welcome');
```

---

## الگوهای پیشنهادی

### ۱. افزونه روی اپ پایه

```php
// com_my_reports/app.php
'depends' => ['com_base_shop' => '>=3'],
```

ترتیب نصب: اول فروشگاه پایه، بعد افزونه گزارش.

### ۲. یکپارچگی اختیاری

```php
'depends' => [
    'com_base_shop' => '>=1',
    'com_sms_gateway' => ['optional' => true],
],
```

```php
if (use_app('com_sms_gateway')->exists()) {
    // ارسال SMS
}
```

### ۳. احراز هویت / manager مشترک

```php
'depends' => ['com_pinoox_manager'],
```

مدل‌های manager را با namespace کامل یا `use_app()->class()` بگیرید.

### ۴. منطق میزبان را کپی نکنید

برای ثبت route روی اپ میزبان ترجیح دهید `extends` + `when()` در `boot.php`.  
برای خواندن منابع میزبان از یک اپ مستقل از `depends` + `use_app()` استفاده کنید.

---

## مستندات مرتبط

- [مرجع app.php](./app-manifest.md)
- [Pinx CLI](./pinx-cli.md)
- [مرجع CLI](./cli-reference.md)
- [boot.php و رویدادها](../advanced/boot-and-events.md)
- [CLI وابستگی‌ها (`deps`)](./deps-cli.md)

---

[← بازگشت به فهرست](../README.md)
