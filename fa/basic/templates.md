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
| `{{ vite_tags('src/main.js')\|raw }}` | تگ‌های Vite |

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

Build:

```bash
php pinoox theme:frontend build com_acme_shop
```

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

## نکات

- فقط سینتکس استاندارد Twig؛ پینوکس helperهای بالا را اضافه می‌کند
- برای SEO صفحات عمومی، HTML کامل در Twig رندر کنید
- تغییر theme در runtime: `View::changeTheme('panel')`

---

## مستندات مرتبط

- [View — ویو](views.md)
- [URL — آدرس](url.md)
- [زبان](language.md)
- [ساختار پروژه](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)
