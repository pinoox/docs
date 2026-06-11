# Twig Templates

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x는 server-side rendering에 기본적으로 **Twig**를 사용합니다. Template file은 `apps/{package}/theme/{theme}/`에 있습니다.

---

## 권장 구조

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

## Layout과 extends

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

## Pinoox Twig helper

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

이 file은 보통 `View::jsResponse('pinoox.twig')`를 통해 `@pinooxjs` route에서 제공됩니다.

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

## Filter와 condition

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## Template cache

배포 후:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## Tips

- 표준 Twig syntax 사용; Pinoox가 위 helper를 추가합니다.
- public SEO 페이지는 Twig에서 full HTML 렌더링 (빈 `#app`만 두지 마세요).
- runtime에 theme 변경: `View::changeTheme('panel')`.

---

## 관련 문서

- [Views](./views.md)
- [URL](./url.md)
- [Language](./language.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)
