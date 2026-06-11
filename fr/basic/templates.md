# Modèles Twig

[← Retour à l'index](../README.md)

Pinoox 3.x utilise **Twig** par défaut pour le rendu côté serveur. Les fichiers de modèle se trouvent dans `apps/{package}/theme/{theme}/`.

---

## Structure recommandée

```
apps/com_acme_shop/theme/default/
├── theme.php              # manifeste du thème (métadonnées)
├── layout.twig            # layout principal
├── main.twig              # shell SPA (optionnel)
├── pinoox.twig            # config JS globale PINOOX
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## Layout et extends

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

## Helpers Twig Pinoox

| Helper | Rôle |
|--------|---------|
| `{{ url().app }}` | URL de base de l'app |
| `{{ url('products') }}` | lien de route |
| `{{ assets('dist/app.js') }}` | fichier du thème |
| `{{ t('welcome.title') }}` | traduction |
| `{{ seo_tags()\|raw }}` | balises meta SEO |
| `{{ vite_tags('src/main.js')\|raw }}` | balises Vite |

---

## `pinoox.twig` — bootstrap frontend

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

Ce fichier est généralement servi depuis la route `@pinooxjs` via `View::jsResponse('pinoox.twig')`.

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

Build :

```bash
php pinoox theme:frontend build com_acme_shop
```

---

## Filtres et conditions

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Cache des modèles

Après déploiement :

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Conseils

- Utilisez la syntaxe Twig standard ; Pinoox ajoute les helpers ci-dessus.
- Pour les pages SEO publiques, rendez du HTML complet dans Twig (pas seulement un `#app` vide).
- Changez de thème à l'exécution : `View::changeTheme('panel')`.

---

## Documentation associée

- [Views](./views.md)
- [URL](./url.md)
- [Langue](./language.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)
