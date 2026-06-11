# Twig テンプレート

[← 索引に戻る](../README.md)

Pinoox 3.x はサーバーサイドレンダリングにデフォルトで **Twig** を使用します。テンプレートファイルは `apps/{package}/theme/{theme}/` にあります。

---

## 推奨構造

```
apps/com_acme_shop/theme/default/
├── theme.php              # テーママニフェスト（メタデータ）
├── layout.twig            # メインレイアウト
├── main.twig              # SPA シェル（任意）
├── pinoox.twig            # グローバル PINOOX JS 設定
├── partials/
│   └── head.twig
└── pages/
    └── home.twig
```

---

## レイアウトと extends

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

## Pinoox Twig ヘルパー

| ヘルパー | 目的 |
|--------|---------|
| `{{ url().app }}` | アプリベース URL |
| `{{ url('products') }}` | ルートリンク |
| `{{ assets('dist/app.js') }}` | テーマファイル |
| `{{ t('welcome.title') }}` | 翻訳 |
| `{{ seo_tags()\|raw }}` | SEO メタタグ |
| `{{ vite_tags('src/main.js')\|raw }}` | Vite タグ |

---

## `pinoox.twig` — フロントエンドブートストラップ

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

このファイルは通常 `@pinooxjs` ルートから `View::jsResponse('pinoox.twig')` で配信されます。

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

ビルド:

```bash
php pinoox theme:frontend build com_acme_shop
```

---

## フィルターと条件

```twig
{% if user %}
    Hello {{ user.name|default('Guest') }}
{% endif %}

{{ created_at|date('Y-m-d') }}
```

---

## テンプレート Cache

デプロイ後:

```bash
php pinoox cache:build com_acme_shop --only=twig
```

---

## ヒント

- 標準 Twig 構文を使用。Pinoox が上記ヘルパーを追加します。
- 公開 SEO ページには Twig で完全な HTML をレンダリング（空の `#app` のみにしない）。
- 実行時にテーマを変更: `View::changeTheme('panel')`。

---

## 関連ドキュメント

- [View](./views.md)
- [URL](./url.md)
- [言語](./language.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)
