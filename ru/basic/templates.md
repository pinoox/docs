# Шаблоны Twig

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x по умолчанию использует **Twig** для серверного рендеринга. Файлы шаблонов находятся в `apps/{package}/theme/{theme}/`.

---

## Рекомендуемая структура

```
apps/com_acme_shop/theme/default/
├── theme.php              # манифест темы (метаданные)
├── layout.twig            # основной layout
├── main.twig              # SPA-оболочка (опционально)
├── pinoox.twig            # глобальная JS-конфигурация PINOOX
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## Layout и extends

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

## Хелперы Twig в Pinoox

| Хелпер | Назначение |
|--------|---------|
| `{{ url().app }}` | базовый URL приложения |
| `{{ url('products') }}` | ссылка на маршрут |
| `{{ assets('dist/app.js') }}` | файл темы |
| `{{ t('welcome.title') }}` | перевод |
| `{{ seo_tags()\|raw }}` | SEO meta-теги |
| `{{ vite_tags('src/main.js')\|raw }}` | теги Vite |

---

## `pinoox.twig` — bootstrap фронтенда

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

Обычно этот файл отдаётся через маршрут `@pinooxjs` с помощью `View::jsResponse('pinoox.twig')`.

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

Сборка:

```bash
php pinoox theme:frontend build com_acme_shop
```

---

## Фильтры и условия

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Кэш шаблонов

После деплоя:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Советы

- Используйте стандартный синтаксис Twig; Pinoox добавляет хелперы выше.
- Для публичных SEO-страниц рендерите полный HTML в Twig (не только пустой `#app`).
- Смена темы во время выполнения: `View::changeTheme('panel')`.

---

## Связанные документы

- [Views](./views.md)
- [URL](./url.md)
- [Язык и перевод](./language.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)
