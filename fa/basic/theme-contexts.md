# کانتکست تم (Theme contexts)

[← بازگشت به فهرست](../README.md)

از **کانتکست تم** وقتی استفاده کنید که یک اپ به **چند سیستم تم مستقل** نیاز دارد — مثلاً سایت عمومی، پنل ادمین، بخش کودک، یا شِل موبایل — که هر کدام پوشه Twig/Vite و زنجیره ارث‌بری خودش را دارد.

این با **ارث‌بری تم** (`extends` در `theme.php` / `theme-extends`) فرق دارد؛ ارث‌بری قالب‌ها را داخل یک تم فعال لایه‌بندی می‌کند. کانتکست مشخص می‌کند **کدام درخت تم** برای درخواست جاری فعال است.

---

## راه‌اندازی سریع

### ۱. تعریف کانتکست‌ها در `app.php`

```php
return [
    'package' => 'com_my_shop',

    // کانتکست پیش‌فرض وقتی Flow وصل نشده
    'theme-context' => 'site',

    'theme-contexts' => [
        'site' => [
            'theme' => 'default',
            'extends' => ['base'],
        ],
        'panel' => [
            'theme' => 'admin',
            'extends' => ['admin-base'],
            'frontend' => [
                'stack' => 'vue',
                'entry' => 'src/main.js',
            ],
        ],
        'kids' => [
            'theme' => 'kids',
            'extends' => ['site'],
        ],
    ],

    'alias' => array_merge([
        'auth' => AuthFlow::class,
    ], theme_flow_aliases(['site', 'panel', 'kids'])),
];
```

چیدمان پوشه‌ها:

```text
apps/com_my_shop/theme/
├── default/     # site
├── admin/       # panel
├── kids/
├── base/
└── admin-base/
```

### ۲. اتصال کانتکست به مسیرها (Flow)

```php
// routes/web.php
use function Pinoox\Router\{collection, get};

collection(path: '/', routes: __DIR__ . '/site.php', flows: ['theme.site']);

collection(path: '/panel', routes: __DIR__ . '/panel.php', flows: ['auth', 'theme.panel']);

collection(path: '/kids', routes: __DIR__ . '/kids.php', flows: ['theme.kids']);
```

هر collection با استک تم خودش رندر می‌شود.

---

## گزینه‌های کانتکست

| کلید | توضیح |
|------|--------|
| `theme` | نام پوشه تم زیر `theme/` |
| `extends` / `theme-extends` | تم(های) والد برای ارث‌بری |
| `path-theme` | مسیر ریشه تم (پیش‌فرض `theme`) |
| `frontend` | تنظیمات Vite/stack مخصوص این کانتکست |

---

## API زمان اجرا

### فعال‌سازی برای درخواست جاری

```php
theme_context('panel');
return View::render('dashboard.twig');
```

Portal:

```php
ThemeContext::activate('panel');
```

### سوییچ موقت

```php
$html = within_theme('panel', fn () => View::render('users.twig'));
```

### بررسی کانتکست فعال

```php
ThemeContext::active();   // مثلاً panel
ThemeContext::info();     // context، نام تم، مسیرهای استک
ThemeStack::resolve();    // با احترام به کانتکست فعال
```

---

## هلپر aliasهای Flow

`theme_flow_aliases()` آبجکت‌های Flow آماده ثبت می‌کند:

```php
theme_flow_aliases(['site', 'panel', 'kids']);

// نام Flow روی مسیر:
// theme.site, theme.panel, theme.kids
```

در `app.php` داخل `'alias'` ادغام کنید.

---

## کنترلر بدون تفکیک collection

```php
public function previewKidsArea()
{
    return within_theme('kids', fn () => View::render('landing.twig'));
}
```

---

## سازگاری عقب‌رو

اگر `theme-contexts` **خالی یا حذف** باشد، رفتار مثل قبل است:

```php
'theme' => 'default',
```

اپ‌های تک‌تم نیازی به تغییر ندارند.

---

## سایت در برابر پنل در برابر API

| بخش | کانتکست رایج | نکته |
|-----|--------------|------|
| سایت عمومی | `site` | شِل Twig سئو، Vite مارکتینگ |
| پنل ادمین | `panel` | تم ادمین جدا + Flow احراز هویت |
| UX محدود / کودک | `kids` | می‌تواند از قالب‌های `site` ارث ببرد |
| مسیرهای JSON API | *(هیچ)* | Flowهای `theme.*` وصل نکنید |

Flowهای `theme.*` را فقط روی مسیرهایی بگذارید که HTML رندر می‌کنند.

---

## مستندات مرتبط

- [مانیفست تم (`theme.php`)](./theme-manifest.md)
- [فرانت‌اند و Vite](./frontend-vite.md)
- [قالب Twig](./templates.md)
- [روتر](./routers.md)
- [boot.php و رویدادها](../advanced/boot-and-events.md)
- [مرجع app.php](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)
