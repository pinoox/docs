# View

[← 索引に戻る](../README.md)

Pinoox 3.x では HTML ページはテーマフォルダ内の **Twig** でレンダリングされます。Controller での標準的な方法: Portal から **`View::render()`**。

---

## テーマ構造

```
apps/com_acme_shop/
├── app.php                 # 'theme' => 'default'
└── theme/default/
    ├── main.twig
    ├── layout.twig
    └── pages/
        └── home.twig
```

---

## Controller でレンダリング（標準）

```php
use Pinoox\Portal\View;

public function index()
{
    return View::render('pages/home', [
        'title' => 'Shop',
        'products' => ProductModel::latest()->take(6)->get(),
    ]);
}
```

`.twig` 拡張子は含めません。View がファイルを自動解決します。

**`view()`** ヘルパーも存在し `View::ready()` を返しますが、Controller では **`View::render()`** を優先してください。

```php
// ヘルパー相当 — 主にエンジン上の set/exists 用
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // 稀
```

---

## 文字列出力（Response なし）

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// メール、PDF など
```

**`render()`** ヘルパーは `View::render()` を直接呼び出します。

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

HTTP `Response` 内に Twig コンテンツを返します。

---

## すべての View 向けグローバルデータ

```php
View::set('siteName', config('app.name'));
// または
view()->set('siteName', config('app.name'));
```

Twig 内:

```twig
<title>{{ siteName }} — {{ title }}</title>
```

---

## SEO（Pinoox 3.x）

```php
View::shareSeo([
    'title' => 'Products',
    'description' => 'Shop product list',
    'canonical' => url('products'),
    'image' => assets('dist/og-cover.jpg'),
]);

return View::render('pages/products');
```

`partials/head.twig` 内:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — Vite 付きシェル

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

詳細は [Twig テンプレート](./templates.md) を参照してください。

---

## View の存在確認

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## ヒント

- ビジネスロジックは Controller/Component に。Twig はプレゼンテーションのみ
- アクティブテーマは `app.php` → `'theme'` から取得
- 純粋な JSON には `response()->json()` または `ApiController` を使用

---

## 関連ドキュメント

- [Twig テンプレート](./templates.md)
- [URL とアセット](./url.md)
- [HTTP Response](./responses.md)
- [Portal](./portal.md)
- [プロジェクト構造](../start/structure.md)

---

[← 索引に戻る](../README.md)
