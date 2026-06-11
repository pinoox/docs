# Views

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x에서 HTML 페이지는 theme 폴더의 **Twig**로 렌더링합니다. Controller의 표준 방법: Portal의 **`View::render()`**.

---

## Theme 구조

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

## Controller에서 렌더링 (표준)

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

`.twig` extension을 포함하지 마세요; View가 file을 자동으로 resolve합니다.

**`view()`** helper도 있으며 `View::ready()`를 반환하지만 Controller에서는 **`View::render()`**를 권장합니다:

```php
// Helper equivalent — mainly for set/exists on the engine
view('pages/home', ['title' => 'Shop']);
return view()->getContentReady();  // rare
```

---

## String 출력 (Response 없음)

```php
$html = render('emails/welcome', ['name' => 'Alex']);
// email, PDF, …
```

**`render()`** helper는 `View::render()`를 직접 호출합니다.

---

## `View::response()`

```php
return View::response('pages/home', ['title' => 'Home']);
```

HTTP `Response` 안에 Twig content를 반환합니다.

---

## 모든 view에 대한 전역 data

```php
View::set('siteName', config('app.name'));
// or
view()->set('siteName', config('app.name'));
```

Twig에서:

```twig
<title>{{ siteName }} — {{ title }}</title>
```

---

## SEO (Pinoox 3.x)

```php
View::shareSeo([
    'title' => 'Products',
    'description' => 'Shop product list',
    'canonical' => url('products'),
    'image' => assets('dist/og-cover.jpg'),
]);

return View::render('pages/products');
```

`partials/head.twig`에서:

```twig
{{ seo_tags()|raw }}
```

---

## SPA — Vite shell

```php
return View::render('main');   // theme/default/main.twig + vite_tags()
```

자세한 내용은 [Twig Templates](./templates.md) 참조.

---

## view 존재 여부 확인

```php
if (View::exists('pages/dashboard')) {
    return View::render('pages/dashboard');
}
return View::render('errors/404');
```

---

## Tips

- business logic은 Controller/Component에; Twig는 presentation만
- 활성 theme는 `app.php` → `'theme'`에서
- pure JSON은 `response()->json()` 또는 `ApiController` 사용

---

## 관련 문서

- [Twig Templates](./templates.md)
- [URL and Assets](./url.md)
- [HTTP Response](./responses.md)
- [Portal](./portal.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)
