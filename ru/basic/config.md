# Конфигурация

[← Вернуться к оглавлению](../README.md)

Настройки Pinoox 3.x хранятся в PHP-файлах в `config/` (ядро и приложение). Стандартный подход: **`config('key')`** для чтения и **`config('name')->set(...)->save()`** для записи.

---

## Чтение

```php
// Простой ключ
$siteName = config('app.name');

// Вложенный ключ (точечная нотация)
$merchant = config('payment.merchant_id');

// Значение по умолчанию
$timeout = config('api.timeout', 30);

// Объект конфигурации для цепочки вызовов
$payment = config('payment');
$enabled = $payment->get('enabled', false);
```

---

## Запись и сохранение

**Всегда вызывайте `save()` после изменений:**

```php
config('payment')->set('enabled', true)->save();

config('payment')->merge([
    'terminal_name' => 'Stripe',
    'merchant_id' => '1234567890',
    'callback_url' => url('payment/callback'),
])->save();
```

---

## Вложенные данные — `setLinear` / `getLinear`

```php
// Чтение
$themeName = config('theme.panel.name');

// Запись
config('theme')->setLinear('panel', 'custom_css', 'panel.css')->save();

config('modules')->setLinear('blog', 'active', true)->save();
```

---

## Расположение файлов конфигурации

| Расположение | Содержимое |
|----------|----------|
| `pincore/config/*.config.php` | Настройки ядра (БД, домен, …) |
| `apps/{package}/config/*.config.php` | Настройки приложения |
| `pinker/config/` | Запечённая версия (production) |
| `pinker/state/config/` | Переопределения после установки (например, БД) |

В разработке чувствительные значения читаются из `.env` через `env()` / `_env()`.

---

## Пример: настройки платёжного шлюза

```php
// apps/com_acme_shop/config/payment.config.php
return [
    'enabled' => false,
    'driver' => 'stripe',
    'merchant_id' => '',
    'callback_url' => '',
];
```

```php
// Controller или Component
if (!config('payment.enabled')) {
    return response()->json(['error' => 'Payment gateway is disabled'], 503);
}

$merchant = config('payment.merchant_id');
```

---

## Пример: динамическое меню

```php
$menu = config('menu')->get('sidebar.children', []);
$menu[] = ['label' => 'Reports', 'route' => 'reports'];
config('menu')->setLinear('sidebar', 'children', $menu)->save();
```

---

## Портал — `Pinoox\Portal\Config`

```php
use Pinoox\Portal\Config;

Config::name('payment')->get('merchant_id');
Config::name('payment')->set('enabled', true)->save();
```

На практике `config()` оборачивает тот же Portal — достаточно одного стиля.

---

## Советы

- Не коммитьте секреты (API-ключи, пароли БД) в git; используйте `.env` или `pinker/state`.
- Имя файла: `{name}.config.php` → `config('{name}.key')`.
- После деплоя в production выполните `php pinoox pinker:rebuild` для запекания конфигурации.

---

## Связанные документы

- [Portal](./portal.md)
- [Pinker](../advanced/pinker.md)
- [Пути к файлам](./path.md)
- [Манифест app.php](../start/app-manifest.md)

---

[← Вернуться к оглавлению](../README.md)
