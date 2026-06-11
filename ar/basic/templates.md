# قوالب Twig

[← العودة إلى الفهرس](../README.md)

يستخدم Pinoox 3.x **Twig** افتراضيًا للعرض من جانب الخادم. ملفات القوالب في `apps/{package}/theme/{theme}/`.

---

## البنية الموصى بها

```
apps/com_acme_shop/theme/default/
├── theme.php              # theme manifest (metadata)
├── layout.twig            # main layout
├── main.twig              # SPA shell (optional)
├── pinoox.twig            # global PINOOX JS config
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## التخطيط و extends

```twig
{# layout.twig #}
<!doctype html>
<html lang="en">
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

## مساعدات Twig في Pinoox

| المساعد | الغرض |
|--------|---------|
| `{{ url().app }}` | عنوان URL الأساسي للتطبيق |
| `{{ url('products') }}` | رابط مسار |
| `{{ assets('dist/app.js') }}` | ملف القالب |
| `{{ t('welcome.title') }}` | ترجمة |
| `{{ seo_tags()\|raw }}` | وسوم SEO |
| `{{ vite_tags('src/main.js')\|raw }}` | وسوم Vite |

---

## `pinoox.twig` — إقلاع الواجهة الأمامية

```twig
const PINOOX = {
    URL: {
        APP: '{{ url().app }}',
        API: '{{ url().api }}',
        SITE: '{{ url().site }}',
        THEME: '{{ assets() }}',
    },
    LANG: '{{ app().config('lang') }}',
};
```

يُقدَّم هذا الملف عادةً من مسار `@pinooxjs` عبر `View::jsResponse('pinoox.twig')`.

---

## SPA + Vite

```twig
{# main.twig #}
<!doctype html>
<html lang="en">
<head>
    {% include 'partials/head.twig' %}
    {{ vite_tags('src/main.js')|raw }}
</head>
<body>
    <div id="app"></div>
</body>
</html>
```

البناء:

```bash
php pinoox theme:frontend build com_acme_shop
```

---

## المرشحات والشروط

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## ذاكرة التخزين المؤقت للقوالب

بعد النشر:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## نصائح

- استخدم صيغة Twig القياسية؛ Pinoox يضيف المساعدات أعلاه.
- لصفحات SEO العامة، اعرض HTML كاملًا في Twig (وليس `#app` فارغًا فقط).
- غيّر القالب وقت التشغيل: `View::changeTheme('panel')`.

---

## وثائق ذات صلة

- [العروض (Views)](./views.md)
- [URL](./url.md)
- [اللغة والترجمة](./language.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)
