# کانتکست تم (Theme contexts)

[← بازگشت به فهرست](../README.md)

**کانتکست تم اختیاری است.** بیشتر اپ‌ها با یک تم (`'theme' => 'default'`) کار می‌کنند و هرگز `theme-contexts` تعریف نمی‌کنند. این قابلیت فقط وقتی لازم است که یک پکیج چند **محیط UI مستقل** داشته باشد (مثلاً سایت عمومی + پنل مدیریت)، هر کدام با درخت تم، پیشوند URL و در صورت نیاز تنظیمات احراز هویت کلاینت جدا.

اگر `theme-contexts` ننویسید، هیچ چیز دیگری عوض نمی‌شود — روتینگ، ویو و bootstrap مثل قبل کار می‌کنند.

این با **ارث‌بری تم** (`extends` در `theme.php` / `theme-extends`) فرق دارد؛ ارث‌بری قالب‌ها را *داخل* یک تم فعال لایه‌بندی می‌کند. کانتکست مشخص می‌کند **کدام درخت تم** برای درخواست جاری فعال است.

---

## چه زمانی استفاده کنید (و چه زمانی نه)

| وقتی کانتکست تم مفید است | وقتی لازم نیست |
|--------------------------|----------------|
| سایت و پنل یک اپ هستند ولی تم جدا می‌خواهند | اپ فقط یک UI دارد |
| پنل زیر پیشوند (`/panel`) است و loginUrl خودش را می‌خواهد | فقط ارث‌بری قالب (`extends`) کافی است |
| Client bootstrap باید `loginUrl` / `url.BASE` / `url.AREA` جدا per محیط بدهد | مسیرها فقط JSON API هستند (بدون تم HTML) |

---

## اپ تک‌تم (بدون کانتکست)

```php
// app.php — برای اکثر اپ‌ها کافی است
return [
    'package' => 'com_my_shop',
    'theme' => 'default',
];
```

بدون `theme-contexts`، بدون فلو `theme.*`، بدون `collection(context: …)`.

---

## راه‌اندازی سریع (چندمحیطی اختیاری)

### ۱. تعریف کانتکست‌ها در `app.php`

```php
return [
    'package' => 'com_my_shop',

    // کانتکست پیش‌فرض وقتی فلو تم روی درخواست اجرا نشده
    'theme-context' => 'site',

    'theme-contexts' => [
        'site' => [
            'path' => '',                    // اختیاری؛ خالی = ریشه اپ
            'theme' => 'default',
            'extends' => ['base'],
            'auth' => [                      // overlay اختیاری برای کلاینت
                'client' => ['loginUrl' => '/login'],
            ],
        ],
        'panel' => [
            'path' => 'panel',               // پیشوند URL + BASE کلاینت
            'theme' => 'admin',
            'extends' => ['admin-base'],
            'auth' => [
                'client' => ['loginUrl' => '/panel/auth/login'],
            ],
            'frontend' => [                  // override اختیاری Vite
                'stack' => 'vue',
                'entry' => 'src/main.js',
            ],
        ],
    ],

    'alias' => array_merge([
        'auth' => AuthFlow::class,
    ], theme_flow_aliases(['site', 'panel'])),
];
```

چیدمان پوشه‌ها:

```text
apps/com_my_shop/theme/
├── default/     # site
├── admin/       # panel
├── base/
└── admin-base/
```

### ۲. اتصال کانتکست به مسیرها

**پیشنهادی** — فقط `context` بدهید؛ مسیر و فلو `theme.*` از `theme-contexts` پر می‌شود:

```php
// routes/web.php
use function Pinoox\Router\{collection, get};

collection(context: 'site', routes: __DIR__ . '/site.php');

collection(context: 'panel', routes: __DIR__ . '/panel.php', flows: ['auth']);
```

هسته وقتی `context` مقدار دارد به‌صورت خودکار:

1. فلو `theme.{context}` را به `flows` اضافه می‌کند (اگر نباشد)
2. اگر `path` خالی باشد، از `theme-contexts[context]['path']` می‌خواند
3. اگر نام کانتکست ناشناخته باشد (و `theme-contexts` تعریف شده باشد) خطا می‌دهد

**فرم صریح** (هنوز معتبر؛ همان نتیجه):

```php
collection(path: '/', routes: __DIR__ . '/site.php', flows: ['theme.site']);

collection(path: '/panel', routes: __DIR__ . '/panel.php', flows: ['auth', 'theme.panel']);
```

می‌توانید ترکیب کنید: `path` غیرخالی صریح بر path کانتکست اولویت دارد و همچنان از `context` برای فلو تم استفاده کنید.

---

## راهنما: path، BASE، AREA و loginUrl

این کلیدها **به‌ازای هر کانتکست اختیاری**اند. وقتی پنل (یا محیط دیگر) نباید ریدایرکت احراز هویت یا base کلاینت سایت را به اشتراک بگذارد از آن‌ها استفاده کنید.

| کلید | اثر |
|------|-----|
| `path` | پیشوند برای `collection(context: …)`. در زمان رندر، path غیرخالی به `window.__PINOOX__.url.BASE` وصل می‌شود (فقط path، مثلاً `/myapp/panel`) و `url.AREA` مطلق می‌شود (مثلاً `https://domain.com/myapp/panel`). |
| `auth.client` | روی `auth.client` سطح اپ برای `__PINOOX__.auth` overlay می‌شود (مثلاً `loginUrl`). اگر اپ `auth.client => false` باشد، auth از کلاینت حذف می‌ماند. |

کلیدهای URL بوت‌استرپ برای محیط فعال:

| کلید | مثال (اپ در `/`، path پنل `panel`) | معنی |
|------|-------------------------------------|------|
| `url.APP` | `https://domain.com` | URL مطلق اپ (فقط segment روتر) |
| `url.BASE` | `/panel` | base فقط‌مسیرِ **محیط فعال** |
| `url.AREA` | `https://domain.com/panel` | URL مطلقِ **محیط فعال** (`APP` + `path` کانتکست) |

بدون path کانتکست (یا سایت با `path: ''`)، مقدار `AREA` برابر `APP` است و `BASE` همان path عادی اپ می‌ماند.

مدل ذهنی:

```text
درخواست /panel/dashboard
  → collection با context "panel"
  → فلو theme.panel → ThemeContext فعال می‌شود
  → Twig/Vite تم admin را می‌گیرند
  → pinoox_bootstrap():
       BASE = /panel
       AREA = https://domain.com/panel
       auth.loginUrl = /panel/auth/login
```

---

## گزینه‌های کانتکست

| کلید | اجباری؟ | توضیح |
|------|---------|--------|
| `theme` | توصیه‌شده | نام پوشه تم زیر `theme/` |
| `extends` / `theme-extends` | خیر | تم(های) والد برای ارث‌بری |
| `path-theme` | خیر | مسیر ریشه تم (پیش‌فرض `theme`) |
| `path` | خیر | پیشوند URL برای collection و `url.BASE` / `url.AREA` کلاینت |
| `auth` | خیر | overlay احراز هویت (`client.loginUrl` و …) |
| `frontend` | خیر | تنظیمات Vite/stack مخصوص این کانتکست |

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

`theme_flow_aliases()` ورودی‌های تو در توی alias برای `app.php` می‌سازد:

```php
theme_flow_aliases(['site', 'panel']);

// نام Flow روی مسیر: theme.site, theme.panel
```

داخل `'alias'` ادغام کنید. بدون alias، `flows: ['theme.panel']` / `collection(context: 'panel')` کلاس فلو را پیدا نمی‌کند.

---

## مثال کامل: سایت + پنل

**`app.php` (خلاصه)**

```php
'theme-context' => 'site',
'theme-contexts' => [
    'site' => [
        'path' => '',
        'theme' => 'default',
        'auth' => ['client' => ['loginUrl' => '/login']],
    ],
    'panel' => [
        'path' => 'panel',
        'theme' => 'panel',
        'auth' => ['client' => ['loginUrl' => '/panel/auth/login']],
    ],
],
'alias' => array_merge(
    ['auth' => AuthFlow::class],
    theme_flow_aliases(['site', 'panel']),
),
```

**`routes/web.php`**

```php
use function Pinoox\Router\collection;

collection(context: 'site', routes: __DIR__ . '/site/web.php');
collection(context: 'panel', routes: __DIR__ . '/panel/web.php', flows: ['auth']);
```

**`routes/panel/web.php`**

```php
use function Pinoox\Router\get;

get('/', [Panel\HomeController::class, 'index'])->name('panel.home');
get('/auth/login', [Panel\AuthController::class, 'login'])->name('panel.login');
```

کاربر احرازنشدهٔ پنل با `loginUrl` پنل ریدایرکت می‌شود؛ SPA همان مقدار را از `window.__PINOOX__.auth` می‌خواند.

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

- حذف یا خالی بودن `theme-contexts` → رفتار تک‌تم (`'theme' => '…'`).
- فرم صریح `path` + `flows: ['theme.panel']` همچنان کار می‌کند.
- اپ‌های موجود **مجبور نیستند** به `collection(context: …)` مهاجرت کنند.

---

## سایت در برابر پنل در برابر API

| بخش | کانتکست رایج | نکته |
|-----|--------------|------|
| سایت عمومی | `site` | شِل Twig سئو، Vite مارکتینگ |
| پنل ادمین | `panel` | تم جدا + فلو احراز هویت |
| UX محدود / کودک | `kids` | می‌تواند از قالب‌های `site` ارث ببرد |
| مسیرهای JSON API | *(هیچ)* | فلوهای `theme.*` وصل نکنید |

فلوهای `theme.*` را فقط روی مسیرهایی بگذارید که HTML رندر می‌کنند.

---

## dev فرانت‌اند با contextها

وقتی site و panel هر کدام تم Vite جدا دارند (`package.json` + `frontend.config.php`):

```bash
php pinoox fe com_my_shop dev --theme=site
php pinoox fe com_my_shop dev --theme=panel
php pinoox fe com_my_shop dev --theme=all
php pinoox fe com_my_shop install --theme=all
php pinoox fe com_my_shop build --theme=panel
```

بلوک‌های `frontend` هر context در `app.php` با `frontend.config.php` همان تم merge می‌شوند. [فرانت‌اند و Vite](./frontend-vite.md) را ببینید.

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
