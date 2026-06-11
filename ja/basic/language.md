# 言語と翻訳

[← 索引に戻る](../README.md)

Pinoox 3.x は **`lang/{locale}/*.lang.php`** ファイルによる i18n をサポートします。標準的な方法: PHP では **`t('file.key')`** または **`Lang::get('file.key')`**、Twig では **`{{ t('file.key') }}`**。

---

## ファイル構造

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

完全なキー: `welcome.title` → ファイル `welcome` + キー `title`。

---

## PHP での使用

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// 複数形
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Twig での使用

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## ロケールの変更

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

デフォルトロケールは `app.php` → `'lang'` から取得されます。

---

## ネストされたプレースホルダー

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## キーの存在確認

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Controller の例

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

## Validation と Lang

Validation メッセージは `lang/{locale}/validation.lang.php` に置きます（[Validation](./validation.md) 参照）。

---

## ヒント

- キーを論理的にグループ化: `product.title`、`cart.checkout` — 1 つの巨大ファイルにしない
- SPA では `pinoox.twig` 経由で `PINOOX.LANG` にロケールを公開
- Controller に UI 文字列をハードコードしない

---

## 関連ドキュメント

- [Twig テンプレート](./templates.md)
- [Portal](./portal.md)
- [Validation](./validation.md)
- [Helpers](../advanced/helpers.md)

---

[← 索引に戻る](../README.md)
