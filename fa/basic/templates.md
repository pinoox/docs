# قالب Twig

[← بازگشت به فهرست](../README.md)

پینوکس ۳.x به‌صورت پیش‌فرض از **Twig** برای رندر سمت سرور استفاده می‌کند. فایل‌های قالب در `apps/{package}/theme/{theme}/` قرار می‌گیرند.

---

## ساختار پیشنهادی

```
apps/com_acme_shop/theme/default/
├── theme.php              # manifest تم (metadata)
├── layout.twig            # layout اصلی
├── main.twig              # shell SPA (اختیاری)
├── pinoox.twig            # تنظیمات JS سراسری PINOOX
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## layout و extends

```twig
{# layout.twig #}
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    {% include 'partials/head.twig' %}
    <link rel="stylesheet" href="{{ assets('dist/app.css') }}">
</head>
<body>
    {% block content %}{% endblock %}
    <script src="{{ url('dist/pinoox.js') }}"></script>
</body>
</html>
```

```twig
{# pages/home.twig #}
{% extends 'layout.twig' %}

{% block content %}
    <h1>{{ title }}</h1>
    {% for product in products %}
        <article>{{ product.title }}</article>
    {% endfor %}
{% endblock %}
```

---

## helperهای Twig در پینوکس

| Helper | کاربرد |
|--------|--------|
| `{{ url().app }}` | base URL اپ (accessor) |
| `{{ url('products') }}` | لینک route |
| `{{ assets('dist/app.js') }}` | فایل theme |
| `{{ t('welcome.title') }}` | ترجمه |
| `{{ seo_tags()\|raw }}` | meta SEO |
| `{{ vite_tags('src/main.js')\|raw }}` | تگ‌های dev HMR یا production با Vite |
| `{{ vite_css_tags('src/main.js')\|raw }}` | فقط تگ‌های stylesheet |
| `{{ vite_js_tags('src/main.js')\|raw }}` | فقط تگ‌های script |
| `{{ vite_asset('src/logo.png') }}` | URL نسخه‌دار از manifest |

---

## `pinoox.twig` — bootstrap فرانت

```twig
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
    LANG: '{{ app().lang() }}',
};
```

این فایل معمولاً با `View::jsResponse('pinoox.twig')` سرو می‌شود.

---

## SPA + Vite

```twig
{# main.twig #}
<!doctype html>
<html lang="fa">
<head>
    {% include 'partials/head.twig' %}
    {{ vite_tags('src/main.js')|raw }}
</head>
<body>
    <div id="app"></div>
</body>
</html>
```

Build و dev:

```bash
php pinoox fe build com_acme_shop
php pinoox fe dev com_acme_shop
```

برای متغیرهای env، [@pinooxhq/vite-plugin](./vite-plugin.md) و تنظیم mount path به [فرانت‌اند و Vite](./frontend-vite.md) مراجعه کنید.

---

## فیلتر و شرط

```twig
{% if user %}
    سلام {{ user.name|default('مهمان') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## cache قالب

پس از deploy:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## ارث‌بری تم (`theme.php`)

متادیتا و تم‌های والد را در پوشه تم بگذارید، نه در `app.php`:

```php
// theme/toranj/theme.php
return [
    'name' => 'toranj',
    'package' => 'com_my_shop',
    'extends' => ['base'],
];
```

منبع اصلی ارث‌بری: `theme.php` → `extends`. `app.php` → `theme-extends` fallback منسوخ است. مرجع کامل فیلدها: [مانیفست تم](./theme-manifest.md).

---

## چند کانتکست تم

یک اپ می‌تواند بین درخت‌های تم مستقل (site / panel / …) با `theme-contexts` و aliasهای Flow مثل `theme.site`، `theme.panel` جابه‌جا شود. راهنما: [کانتکست تم](./theme-contexts.md).

---

## نکات

- فقط سینتکس استاندارد Twig؛ پینوکس helperهای بالا را اضافه می‌کند
- برای SEO صفحات عمومی، HTML کامل در Twig رندر کنید
- تغییر theme در runtime: `View::changeTheme('panel')` یا `within_theme('panel', …)`

---

## مستندات مرتبط

- [View — ویو](views.md)
- [کانتکست تم](./theme-contexts.md)
- [مانیفست تم (`theme.php`)](./theme-manifest.md)
- [URL — آدرس](url.md)
- [زبان](language.md)
- [فرانت‌اند و Vite](./frontend-vite.md)
- [@pinooxhq/vite-plugin](./vite-plugin.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)
