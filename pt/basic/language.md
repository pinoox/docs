# Idioma e tradução

[← Voltar ao índice](../README.md)

O Pinoox 3.x suporta i18n por meio de arquivos **`lang/{locale}/*.lang.php`**. A abordagem padrão: **`t('file.key')`** ou **`Lang::get('file.key')`** em PHP e **`{{ t('file.key') }}`** no Twig.

---

## Estrutura de arquivos

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

Chave completa: `welcome.title` → arquivo `welcome` + chave `title`.

---

## Uso em PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Pluralização
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Uso no Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Alterar locale

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

O locale padrão vem de `app.php` → `'lang'`.

---

## Placeholders aninhados

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## Verificar se uma chave existe

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Exemplo em controller

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

## Validação e Lang

Coloque mensagens de validação em `lang/{locale}/validation.lang.php` (veja [Validação](./validation.md)).

---

## Dicas

- Agrupe chaves de forma lógica: `product.title`, `cart.checkout` — não um arquivo gigante.
- Para SPAs, exponha o locale em `pinoox.twig` via `PINOOX.LANG`.
- Evite strings de UI fixas nos controllers.

---

## Documentação relacionada

- [Templates Twig](./templates.md)
- [Portal](./portal.md)
- [Validação](./validation.md)
- [Helpers](../advanced/helpers.md)

---

[← Voltar ao índice](../README.md)
