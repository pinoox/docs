# Twig Templates

[← Back to index](../README.md)

Pinoox 3.x uses **Twig** by default for server-side rendering. Template files live in `apps/{package}/theme/{theme}/`.

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

## Layout and extends

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
| `{{ vite_tags('src/main.js')\|raw }}` | Vite dev HMR or production tags |
| `{{ vite_css_tags('src/main.js')\|raw }}` | Stylesheet tags only |
| `{{ vite_js_tags('src/main.js')\|raw }}` | Script tags only |
| `{{ vite_asset('src/logo.png') }}` | Versioned asset URL from manifest |

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

This file is usually served from the `@pinooxjs` route via `View::jsResponse('pinoox.twig')`.

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

Build and dev:

```bash
php pinoox fe build com_acme_shop
php pinoox fe dev com_acme_shop
```

See [Frontend & Vite](./frontend-vite.md) for env variables, `vite.pinoox.mjs`, and mount-path setup.

---

## Filters and conditions

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Template cache

After deploy:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Tips

- Use standard Twig syntax; Pinoox adds the helpers above.
- For public SEO pages, render full HTML in Twig (not an empty `#app` only).
- Change theme at runtime: `View::changeTheme('panel')`.

---

## Related docs

- [Views](./views.md)
- [URL](./url.md)
- [Language](./language.md)
- [Frontend & Vite](./frontend-vite.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../README.md)
