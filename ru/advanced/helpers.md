# Глобальные хелперы (Global Helpers)

[← Назад к оглавлению](../README.md)

Pinoox 3.x загружает глобальные хелперы из `pincore/functions/`. Для повседневной разработки приложений этих хелперов (плюс порталов) достаточно — не создавайте экземпляры базовых компонентов (Components) напрямую.

---

## Основные хелперы

| Хелпер | Назначение | Пример |
|--------|---------|---------|
| `render()` | HTML в виде строки | `$html = render('email', $data);` |
| `response()` | HTTP-ответ | `return response()->json($data);` |
| `redirect()` | Перенаправление | `return redirect(url('login'));` |
| `url()` | URL приложения/сайта | `url('products')` |
| `path()` | Путь к файлу на диске | `path('storage/logs/app.log')` |
| `assets()` | URL файла темы | `assets('dist/app.css')` |
| `config()` | Чтение/запись конфигурации | `config('app.name')` |
| `t()` | Перевод (возврат значения) | `t('welcome.title')` |
| `lang()` | Перевод (вывод echo) | `lang('welcome.title')` |
| `app()` | Активное приложение | `app()->get('package')` |
| `auth()` | Авторизованный пользователь | `auth()` → `Auth::user()` |
| `user()` | Поле пользователя | `user('email')` |
| `isLogin()` | Статус входа | `if (isLogin()) { … }` |
| `session()` | Сессия | `session('token')` |
| `runtime()` | Активное HTTP-ядро | `runtime()->getRequest()` |
| `_env()` | Переменная окружения | `_env('APP_DEBUG', false)` |
| `alias()` | Алиас flow/класса | `alias('auth')` |

Для HTML в контроллерах используйте **`View::render()`** (как в системных приложениях). Хелпер `view()` существует, но в контроллерах предпочитайте Portal.

---

## Request — внедрение зависимости или `runtime()`

Глобального хелпера `request()` в pincore нет. В контроллерах и компонентах используйте внедрение по type-hint:

```php
use Pinoox\Component\Http\Request;

public function save(Request $request)
{
    $title = $request->get('title');
    $page = $request->queryOne('page', 1);
    $email = $request->requestOne('email');
    $all = $request->all();
}
```

Во Flow или там, где сигнатура не позволяет внедрение:

```php
$request = runtime()->getRequest();
$page = $request->get('page', 1);
```

---

## Auth — `auth()`, `user()`, Flow

```php
// Текущий пользователь (Auth::user())
$current = auth();
$name = user('fname');

if (isLogin()) {
    // ...
}

// auth($key) — то же самое, что user($key)
$email = auth('email');

// Защита маршрутов через алиас Flow
// app.php → 'alias' => ['auth' => AuthFlow::class]
// маршруты → ->flows(['auth']) или группа с flows
```

---

## View и Response

```php
use Pinoox\Portal\View;

return View::render('pages/list', ['items' => $items]);

return response()->json(['ok' => true]);

return redirect(url('dashboard'));
```

---

## Конфигурация (Config)

```php
$enabled = config('payment.enabled', false);

config('payment')->set('enabled', true)->save();
```

---

## Язык (Lang)

```php
$label = t('product.title');
// В Twig: {{ t('product.title') }}
```

---

## URL и Path

```php
$link = url('api/v1/orders');
$file = path('storage/export.csv');
$css = assets('dist/panel.css');
```

---

## Собственные хелперы приложения

В `app.php`:

```php
'loader' => [
    '@func' => 'func.php',
],
```

```php
// apps/com_acme_shop/func.php
function format_price(float $amount): string
{
    return '$' . number_format($amount, 2);
}
```

---

## Twig-хелперы (в шаблонах)

Помимо PHP-хелперов, в Twig доступны:

```twig
{{ url().app }}
{{ url('profile') }}
{{ assets('dist/app.js') }}
{{ t('welcome.title') }}
{{ app().name }}
{{ theme().title }}
```

---

## Советы

- Используйте `View::render()` в контроллерах для HTML; хелперы `url()`, `t()` и `config()` — для повседневных задач
- Хелперы работают только после инициализации Pinoox — не подключайте их в «сырых» PHP-скриптах вне `index.php` / `pinoox`
- Для сложной логики предпочитайте `Component/` + Portal вместо собственных хелперов

---

## Связанные документы

- [Portal](../basic/portal.md)
- [URL](../basic/url.md)
- [Path](../basic/path.md)
- [Язык (Language)](../basic/language.md)
- [Сервисы (Services)](./services.md)

---

[← Назад к оглавлению](../README.md)
