# Язык и перевод

[← Вернуться к оглавлению](../README.md)

Pinoox 3.x поддерживает i18n через файлы **`lang/{locale}/*.lang.php`**. Стандартный подход: **`t('file.key')`** или **`Lang::get('file.key')`** в PHP и **`{{ t('file.key') }}`** в Twig.

---

## Структура файлов

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

Полный ключ: `welcome.title` → файл `welcome` + ключ `title`.

---

## Использование в PHP

```php
use Pinoox\Portal\Lang;

echo t('welcome.title');
echo t('welcome.hello', ['name' => 'Alex']);
echo Lang::get('product.add_to_cart');

// Множественное число
echo Lang::choice('welcome.items', 5, ['count' => 5]);
```

---

## Использование в Twig

```twig
<h1>{{ t('welcome.title') }}</h1>
<p>{{ t('welcome.hello', { name: user.name }) }}</p>
```

---

## Смена локали

```php
Lang::setLocale('fa');
$current = Lang::getLocale();   // en
```

Локаль по умолчанию задаётся в `app.php` → `'lang'`.

---

## Вложенные плейсхолдеры

```php
// lang/en/user.lang.php
// 'profile' => 'User: :user.name'

t('user.profile', ['user' => ['name' => 'Sam']]);
```

---

## Проверка существования ключа

```php
if (Lang::has('welcome.title')) {
    // ...
}
```

---

## Пример контроллера

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

## Валидация и Lang

Сообщения валидации размещайте в `lang/{locale}/validation.lang.php` (см. [Validation](./validation.md)).

---

## Советы

- Группируйте ключи логически: `product.title`, `cart.checkout` — не один огромный файл.
- Для SPA экспонируйте локаль в `pinoox.twig` через `PINOOX.LANG`.
- Избегайте жёстко заданных строк интерфейса в контроллерах.

---

## Связанные документы

- [Шаблоны Twig](./templates.md)
- [Portal](./portal.md)
- [Validation](./validation.md)
- [Хелперы](../advanced/helpers.md)

---

[← Вернуться к оглавлению](../README.md)
