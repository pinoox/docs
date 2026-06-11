# 语言与翻译

[← 返回索引](../README.md)

Pinoox 3.x 通过 **`lang/{locale}/*.lang.php`** 文件支持国际化（i18n）。标准方式：在 PHP 中使用 **`t('file.key')`** 或 **`Lang::get('file.key')`**，在 Twig 中使用 **`{{ t('file.key') }}`**。

---

## 文件结构

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

完整键名：`welcome.title` → 文件 `welcome` + 键 `title`。

---

## 在 PHP 中使用

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// 复数化
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## 在 Twig 中使用

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## 切换语言环境

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

默认语言环境来自 `app.php` → `'lang'`。

---

## 嵌套占位符

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## 检查键是否存在

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## 控制器示例

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

## 验证与 Lang

将验证消息放在 `lang/{locale}/validation.lang.php` 中（参见[验证（Validation）](./validation.md)）。

---

## 小贴士

- 按逻辑分组键名：`product.title`、`cart.checkout` — 而不是一个巨大的文件。
- 对于 SPA，在 `pinoox.twig` 中通过 `PINOOX.LANG` 暴露语言环境。
- 避免在控制器中硬编码 UI 字符串。

---

## 相关文档

- [Twig 模板](./templates.md)
- [Portal](./portal.md)
- [验证（Validation）](./validation.md)
- [辅助函数（Helpers）](../advanced/helpers.md)

---

[← 返回索引](../README.md)
