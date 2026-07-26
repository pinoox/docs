# مانیفست تم (`theme.php`)

[← بازگشت به فهرست](../README.md)

هر پوشه تم مانیفست خودش را دارد. **متادیتا و ارث‌بری داخل تم** تعریف می‌شوند — نه در `app.php`.

```text
apps/{package}/theme/{theme-name}/
├── theme.php          ← مانیفست (برای پکیج pinx تم و لیست manager الزامی)
├── main.twig
├── cover.png
└── dist/
```

---

## نمونه استاندارد `theme.php`

```php
<?php

return [
    'name' => 'toranj',
    'package' => 'com_pinoox_paper',
    'extends' => ['blue'],
    'cover' => 'cover.png',
    'developer' => 'pinoox',
    'copyright' => 'MIT',
    'version-name' => '1.0',
    'version-code' => 1,
    'api' => true,
    'title' => [
        'en' => 'Toranj',
        'fa' => 'ترنج',
    ],
    'description' => [
        'en' => 'Minimal blog template',
        'fa' => 'قالب مینیمال وبلاگ',
    ],
];
```

### مرجع فیلدها

| فیلد | الزامی | توضیح |
|------|--------|--------|
| `name` | بله | اسلاگ پوشه تم (معمولاً هم‌نام پوشه) |
| `package` | بله* | پکیج اپ میزبان (`com_pinoox_paper`) |
| `app` | قدیمی | نام مستعار `package` (تم‌های `.pin` قدیمی) |
| `extends` | خیر | تم(های) والد — رشته یا آرایه |
| `cover` | خیر | تصویر پیش‌نمایش نسبت به پوشه تم |
| `developer` | خیر | نویسنده / تیم |
| `copyright` | خیر | مجوز |
| `version-name` / `version` | خیر | برچسب نسخه تم |
| `version-code` / `app_version` | خیر | نسخه عددی تم |
| `api` | خیر | تم شِل API-محور دارد |
| `title` | خیر | نام نمایشی چندزبانه (`en`، `fa`، …) |
| `description` | خیر | توضیح چندزبانه |

\* برای توزیع (`pinx` build تم) و لیست manager الزامی است.

---

## ارث‌بری (`extends`)

والدها را **داخل تم فرزند** تعریف کنید:

```php
return [
    'name' => 'toranj',
    'package' => 'com_pinoox_paper',
    'extends' => ['blue'],
];
```

والد از اپ دیگر:

```php
'extends' => ['default', '@com_pinoox_welcome/welcome'],
```

ترتیب حل:

1. **`theme.php` → `extends`** (اصلی)
2. **`app.php` → `theme-extends`** (fallback منسوخ وقتی مانیفست تم `extends` ندارد)

در `app.php` فقط **نام تم فعال** را بگذارید (یا از [کانتکست تم](./theme-contexts.md) استفاده کنید). ارث‌بری را آنجا تکرار نکنید.

---

## API در PHP

```php
use Pinoox\Component\Template\Theme\ThemeManifest;
use Pinoox\Component\Template\Theme\ThemeStack;

$manifest = ThemeManifest::load('com_pinoox_paper', 'toranj');
$manifest->title('fa');
$manifest->extends();

$themes = ThemeManifest::discover('com_pinoox_paper');

$stack = ThemeStack::resolve('com_pinoox_paper'); // تم فعال + extends مانیفست
```

---

## پکیج‌های pinx تم

ساخت تم قابل توزیع:

```php
// app.php
'pinx' => [
    'type' => 'theme',
    'target_app' => 'com_pinoox_paper',
    'theme_name' => 'toranj',
],
```

```bash
php pinoox pinx:build com_pinoox_paper
```

الزامات:

- وجود `theme/toranj/theme.php`
- مقدار `package` در `theme.php` باید با اپ میزبان یکی باشد
- `manifest.json` داخل `.pinx` شامل `theme_meta` است و نام تم را به‌عنوان شناسه پکیج می‌گیرد

نصب بعد از استخراج `theme.php` را اعتبارسنجی می‌کند.

تم‌های قدیمی `.pin` با `theme.php` در ریشه از طریق `PinxReader` پشتیبانی می‌شوند.

---

## اسکفولد

استاب هنگام ساخت تم:

```text
pincore/stubs/theme.php.stub
```

`php pinoox app:create` فایل `theme/default/theme.php` را از همین استاب می‌نویسد.

---

## یکپارچگی با Manager

`com_pinoox_manager` تم‌ها را با `ThemeManifest::discover()` لیست می‌کند — فقط پوشه‌هایی که `theme.php` دارند در انتخابگر قالب دیده می‌شوند.

---

## مستندات مرتبط

- [کانتکست تم](./theme-contexts.md)
- [قالب Twig](./templates.md)
- [فرانت‌اند و Vite](./frontend-vite.md)
- [Pinx CLI](../start/pinx-cli.md)
- [مرجع app.php](../start/app-manifest.md)

---

[← بازگشت به فهرست](../README.md)
