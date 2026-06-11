# Начало работы с тестированием в Pinoox

[← Вернуться к оглавлению](../README.md)

Pinoox использует единый подход для **ядра фреймворка** (`tests/`) и **каждого приложения** (`apps/{package}/tests/`): [Pest](https://pestphp.com/), общий bootstrap и `AppTestKit`. Это руководство проходит стандартный workflow с практическими примерами.

---

## Стек тестирования

| Инструмент | Роль |
|------|------|
| Pest | Запуск PHP-тестов |
| `Pinoox\Component\Test\AppTestKit` | Загрузка окружения, временное приложение, HTTP-запросы |
| `tests/bootstrap.php` | Общая точка входа для тестов ядра и приложений |

---

## Запуск тестов

```bash
# Все тесты ядра
vendor/bin/pest

# Из CLI (интерактивный выбор пакета)
php pinoox test

# Конкретное приложение
php pinoox test com_my_shop

# Фильтр по имени теста
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Только Feature или Unit
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

В CI можно также использовать скрипты в `composer.json`:

```bash
composer test          # platform tests
composer test:apps     # all app tests
```

---

## Структура папки тестов приложения

Запуск `php pinoox app:create` автоматически создаёт папку `tests/`:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← smoke test
    └── Unit/
```

Создание нового теста:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## Файл `tests/Pest.php`

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

Хелпер `appPackage()` задаёт активное приложение для хелперов и автоопределения.

---

## Глобальные хелперы

| Хелпер | Назначение |
|--------|---------|
| `appPackage($package?)` | Установить / прочитать активный пакет |
| `inApp($package, fn)` | Выполнить код внутри `App::meeting()` |
| `appPath($package, $sub = '')` | Путь к папке приложения |
| `fakeApp($package, $files)` | Создать временное приложение с пользовательскими файлами |
| `deleteFakeApp($package)` | Удалить временное приложение |
| `appGet($package, $uri, ...)` | GET-запрос → `TestResponse` |
| `appPost($package, $uri, $data)` | POST-запрос |
| `appPostJson($package, $uri, $json)` | JSON POST-запрос |
| `pinooxBoot()` | Загрузить тестовое окружение |

---

## Unit — тестирование класса Component

```php
// apps/com_my_shop/tests/Unit/PriceTest.php

it('calculates discount', function () {
    $package = appPackage();

    inApp($package, function () {
        $price = new App\com_my_shop\Component\PriceHelper();
        expect($price->discount(100, 10))->toBe(90);
    });
});
```

---

## Feature — smoke test загрузки приложения

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## Ядро vs приложение

| Расположение | Назначение | Base case |
|----------|---------|-----------|
| `tests/Feature/` | Фреймворк, portals, router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, интеграция | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, чистая логика | `Tests\AppTestCase` |

---

## Режим test

В тестовом окружении `mode` автоматически устанавливается в `test`:

```php
config('~pinoox')->get('mode'); // 'test'
```

В CI настройте `.env.testing` или `APP_ENV=test` при необходимости.

---

## Советы

1. После `fakeApp()` всегда вызывайте `deleteFakeApp()` в `afterEach`.
2. Используйте `inApp()` для config, portals или models внутри приложения.
3. Используйте `appGet` / `appPostJson` для маршрутов и API.
4. Маршруты → **Feature**; классы `Component/` → **Unit**.
5. Используйте `php pinoox test:create` вместо ручного копирования файлов.

---

## Связанные документы

- [HTTP-тесты](./http-tests.md)
- [Тесты консоли](./console-tests.md)
- [Браузерные (HTML) тесты](./browser-tests.md)
- [Тесты базы данных](./database.md)
- [Mocking](./mocking.md)
- [Ваше первое приложение](../start/your-first-app.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)
