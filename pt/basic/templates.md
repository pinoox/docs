# Templates Twig

[← Voltar ao índice](../README.md)

O Pinoox 3.x usa **Twig** por padrão para renderização no servidor. Os arquivos de template ficam em `apps/{package}/theme/{theme}/`.

---

## Estrutura recomendada

```
apps/com_acme_shop/theme/default/
├── theme.php              # manifesto do tema (metadados)
├── layout.twig            # layout principal
├── main.twig              # shell SPA (opcional)
├── pinoox.twig            # config JS global PINOOX
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## Layout e extends

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

## Helpers Twig do Pinoox

| Helper | Propósito |
|--------|---------|
| `{{ url().app }}` | URL base do app |
| `{{ url('products') }}` | link de rota |
| `{{ assets('dist/app.js') }}` | arquivo do tema |
| `{{ t('welcome.title') }}` | tradução |
| `{{ seo_tags()\|raw }}` | meta tags SEO |
| `{{ vite_tags('src/main.js')\|raw }}` | tags Vite |

---

## `pinoox.twig` — bootstrap do frontend

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

Este arquivo costuma ser servido pela rota `@pinooxjs` via `View::jsResponse('pinoox.twig')`.

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

## Filtros e condições

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Cache de templates

Após deploy:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Dicas

- Use sintaxe Twig padrão; o Pinoox adiciona os helpers acima.
- Para páginas públicas com SEO, renderize HTML completo no Twig (não apenas um `#app` vazio).
- Altere o tema em runtime: `View::changeTheme('panel')`.

---

## Documentação relacionada

- [Views](./views.md)
- [URL](./url.md)
- [Idioma](./language.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)
