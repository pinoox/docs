# Twig Templates

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x server-side rendering के लिए default रूप से **Twig** उपयोग करता है। Template files `apps/{package}/theme/{theme}/` में रहती हैं।

---

## Recommended structure

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

## Layout और extends

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

## Pinoox Twig helpers

| Helper | Purpose |
|--------|---------|
| `{{ url().app }}` | app base URL |
| `{{ url('products') }}` | route link |
| `{{ assets('dist/app.js') }}` | theme file |
| `{{ t('welcome.title') }}` | translation |
| `{{ seo_tags()\|raw }}` | SEO meta tags |
| `{{ vite_tags('src/main.js')\|raw }}` | Vite tags |

---

## `pinoox.twig` — frontend bootstrap

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

यह file आमतौर पर `@pinooxjs` route से `View::jsResponse('pinoox.twig')` के ज़रिए serve होती है।

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

Build:

```bash
php pinoox theme:frontend build com_acme_shop
```

---

## Filters और conditions

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Template cache

Deploy के बाद:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Tips

- Standard Twig syntax उपयोग करें; Pinoox ऊपर दिए helpers add करता है।
- Public SEO pages के लिए Twig में full HTML render करें (केवल empty `#app` नहीं)।
- Runtime पर theme बदलें: `View::changeTheme('panel')`.

---

## संबंधित docs

- [Views](./views.md)
- [URL](./url.md)
- [Language](./language.md)
- [Project structure](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
