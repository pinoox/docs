# 视图（Views）

[← 返回索引](../README.md)

在 Pinoox 3.x 中，HTML 页面使用主题文件夹中的 **Twig** 进行渲染。控制器中的标准方式是来自 Portal 的 **`View::render()`**。

---

## 主题结构

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

## 在控制器中渲染（标准方式）

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

不要包含 `.twig` 扩展名；View 会自动解析文件。

**`view()`** 辅助函数也存在并返回 `View::ready()`，但在控制器中建议使用 **`View::render()`**：

```php
// 等价的辅助函数 — 主要用于引擎上的 set/exists
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // 少见
```

---

## 字符串输出（不生成 Response）

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// 邮件、PDF 等
```

**`render()`** 辅助函数会直接调用 `View::render()`。

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

将 Twig 内容包装在 HTTP `Response` 中返回。

---

## 所有视图共享的全局数据

```php
View::set('siteName', config('app.name'));
// 或者
view()->set('siteName', config('app.name'));
```

在 Twig 中：

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

在 `partials/head.twig` 中：

```twig
{{ seo_tags()|raw }}
```

---

## SPA — 配合 Vite 的外壳页面

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

详情请见 [Twig 模板](./templates.md)。

---

## 检查视图是否存在

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## 小贴士

- 业务逻辑放在 Controller/Component 中；Twig 仅用于展示
- 当前激活的主题来自 `app.php` → `'theme'`
- 纯 JSON 请使用 `response()->json()` 或 `ApiController`

---

## 相关文档

- [Twig 模板](./templates.md)
- [URL 与静态资源](./url.md)
- [HTTP 响应（Response）](./responses.md)
- [Portal](./portal.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)
