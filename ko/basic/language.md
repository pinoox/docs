# Language and Translation

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x i18n은 **`lang/{locale}/*.lang.php`** file을 통해 지원합니다. 표준 방법: PHP에서는 **`t('file.key')`** 또는 **`Lang::get('file.key')`**, Twig에서는 **`{{ t('file.key') }}`**.

---

## File 구조

```
apps/com_acme_shop/
├── app.php              # 'lang' => 'en'
└── lang/
    ├── fa/
    │   ├── welcome.lang.php
    │   └── product.lang.php
    └── en/
        └── welcome.lang.php
```

```php
// lang/en/welcome.lang.php
return [
    'title' => 'Welcome to the shop',
    'hello' => 'Hello :name!',
    'items' => 'One item|:count items',
];
```

전체 key: `welcome.title` → file `welcome` + key `title`.

---

## PHP에서 사용

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralization
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Twig에서 사용

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Locale 변경

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

기본 locale은 `app.php` → `'lang'`에서 옵니다.

---

## Nested placeholder

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## key 존재 여부 확인

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Controller 예제

```php
use Pinoox\Portal\View;

public function index()
{
    return View::render('pages/home', [
        'heading' => t('welcome.title'),
        'cta' => t('welcome.shop_now'),
    ]);
}
```

---

## Validation과 Lang

validation message는 `lang/{locale}/validation.lang.php`에 두세요 ([Validation](./validation.md) 참조).

---

## Tips

- key를 논리적으로 그룹화: `product.title`, `cart.checkout` — 하나의 거대 file 금지
- SPA에서는 `pinoox.twig`의 `PINOOX.LANG`으로 locale 노출
- Controller에 UI string을 하드코딩하지 마세요

---

## 관련 문서

- [Twig Templates](./templates.md)
- [Portal](./portal.md)
- [Validation](./validation.md)
- [Helpers](../advanced/helpers.md)

---

[← 색인으로 돌아가기](../README.md)
