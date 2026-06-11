# Twig-Templates

[← Zurück zum Index](../README.md)

Pinoox 3.x verwendet standardmäßig **Twig** für serverseitiges Rendering. Template-Dateien liegen in `apps/{package}/theme/{theme}/`.

---

## Empfohlene Struktur

```
apps/com_acme_shop/theme/default/
├── theme.php              # Theme-Manifest (Metadaten)
├── layout.twig            # Hauptlayout
├── main.twig              # SPA-Shell (optional)
├── pinoox.twig            # globale PINOOX-JS-Config
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## Layout und extends

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

## Pinoox-Twig-Helper

| Helper | Zweck |
|--------|---------|
| `{{ url().app }}` | App-Basis-URL |
| `{{ url('products') }}` | Routen-Link |
| `{{ assets('dist/app.js') }}` | Theme-Datei |
| `{{ t('welcome.title') }}` | Übersetzung |
| `{{ seo_tags()\|raw }}` | SEO-Meta-Tags |
| `{{ vite_tags('src/main.js')\|raw }}` | Vite-Tags |

---

## `pinoox.twig` — Frontend-Bootstrap

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

Diese Datei wird üblicherweise über die Route `@pinooxjs` mit `View::jsResponse('pinoox.twig')` ausgeliefert.

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

## Filter und Bedingungen

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Template-Cache

Nach dem Deploy:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Tipps

- Standard-Twig-Syntax verwenden; Pinoox ergänzt die oben genannten Helper.
- Für öffentliche SEO-Seiten vollständiges HTML in Twig rendern (nicht nur leeres `#app`).
- Theme zur Laufzeit wechseln: `View::changeTheme('panel')`.

---

## Verwandte Dokumentation

- [Views](./views.md)
- [URL](./url.md)
- [Sprache](./language.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zum Index](../README.md)
