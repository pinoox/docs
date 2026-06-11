# Twig 模板

[← 返回索引](../README.md)

Pinoox 3.x 默认使用 **Twig** 进行服务端渲染。模板文件位于 `apps/{package}/theme/{theme}/`。

---

## 推荐结构

```
apps/com_acme_shop/theme/default/
├── theme.php              # 主题清单（元数据）
├── layout.twig            # 主布局
├── main.twig              # SPA 外壳（可选）
├── pinoox.twig            # 全局 PINOOX JS 配置
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## 布局与继承（extends）

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

## Pinoox 的 Twig 辅助函数

| 辅助函数 | 用途 |
|--------|---------|
| `{{ url().app }}` | 应用基础 URL |
| `{{ url('products') }}` | 路由链接 |
| `{{ assets('dist/app.js') }}` | 主题文件 |
| `{{ t('welcome.title') }}` | 翻译 |
| `{{ seo_tags()\|raw }}` | SEO meta 标签 |
| `{{ vite_tags('src/main.js')\|raw }}` | Vite 标签 |

---

## `pinoox.twig` — 前端引导

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

此文件通常通过 `@pinooxjs` 路由由 `View::jsResponse('pinoox.twig')` 提供。

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

构建：

```bash
php pinoox theme:frontend build com_acme_shop
```

---

## 过滤器与条件

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## 模板缓存

部署后执行：

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## 小贴士

- 使用标准 Twig 语法；Pinoox 在此之上增加了上述辅助函数。
- 对于面向公众的 SEO 页面，请在 Twig 中渲染完整 HTML（而不是只有一个空的 `#app`）。
- 在运行时切换主题：`View::changeTheme('panel')`。

---

## 相关文档

- [视图（Views）](./views.md)
- [URL](./url.md)
- [语言（Language）](./language.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)
