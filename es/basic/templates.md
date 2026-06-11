# Plantillas Twig

[← Volver al índice](../README.md)

Pinoox 3.x usa **Twig** por defecto para el renderizado en el servidor. Los archivos de plantilla viven en `apps/{package}/theme/{theme}/`.

---

## Estructura recomendada

```
apps/com_acme_shop/theme/default/
├── theme.php              # manifiesto del tema (metadatos)
├── layout.twig            # layout principal
├── main.twig              # shell SPA (opcional)
├── pinoox.twig            # configuración JS global PINOOX
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## Layout y extends

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

## Helpers de Twig de Pinoox

| Helper | Propósito |
|--------|---------|
| `{{ url().app }}` | URL base de la app |
| `{{ url('products') }}` | enlace de ruta |
| `{{ assets('dist/app.js') }}` | archivo del tema |
| `{{ t('welcome.title') }}` | traducción |
| `{{ seo_tags()\|raw }}` | meta tags de SEO |
| `{{ vite_tags('src/main.js')\|raw }}` | tags de Vite |

---

## `pinoox.twig` — bootstrap del frontend

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

Este archivo normalmente se sirve desde la ruta `@pinooxjs` mediante `View::jsResponse('pinoox.twig')`.

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

## Filtros y condiciones

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Caché de plantillas

Después del despliegue:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Consejos

- Usa la sintaxis estándar de Twig; Pinoox agrega los helpers anteriores.
- Para páginas públicas con SEO, renderiza el HTML completo en Twig (no solo un `#app` vacío).
- Cambia el tema en tiempo de ejecución: `View::changeTheme('panel')`.

---

## Documentación relacionada

- [Vistas](./views.md)
- [URL](./url.md)
- [Idioma](./language.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)
