# Twig şablonları

[← Dizine dön](../README.md)

Pinoox 3.x varsayılan olarak sunucu tarafı render için **Twig** kullanır. Şablon dosyaları `apps/{package}/theme/{theme}/` içinde yer alır.

---

## Önerilen yapı

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

## Layout ve extends

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

## Pinoox Twig helper'ları

| Helper | Amaç |
|--------|---------|
| `{{ url().app }}` | uygulama temel URL'si |
| `{{ url('products') }}` | route bağlantısı |
| `{{ assets('dist/app.js') }}` | tema dosyası |
| `{{ t('welcome.title') }}` | çeviri |
| `{{ seo_tags()\|raw }}` | SEO meta etiketleri |
| `{{ vite_tags('src/main.js')\|raw }}` | Vite etiketleri |

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

Bu dosya genellikle `View::jsResponse('pinoox.twig')` ile `@pinooxjs` route'undan sunulur.

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

Derleme:

```bash
php pinoox theme:frontend build com_acme_shop
```

---

## Filtreler ve koşullar

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Şablon önbelleği

Dağıtımdan sonra:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## İpuçları

- Standart Twig sözdizimini kullanın; Pinoox yukarıdaki helper'ları ekler.
- Herkese açık SEO sayfaları için Twig'de tam HTML render edin (yalnızca boş `#app` değil).
- Çalışma zamanında temayı değiştirin: `View::changeTheme('panel')`.

---

## İlgili dokümantasyon

- [View](./views.md)
- [URL](./url.md)
- [Dil](./language.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)
