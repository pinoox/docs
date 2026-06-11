# Тестирование базы данных в Pinoox

[← Вернуться к оглавлению](../README.md)

Для тестирования моделей, миграций и endpoint, зависящих от БД, выполняйте код внутри `inApp()` и используйте тестовую базу данных (`mode=test`). Запустите миграции перед тестами, чтобы схема была готова.

---

## Предварительные условия

1. В `.env.testing` (или `.env`) настройте тестовую БД, отдельную от production.
2. Один раз выполните миграции приложения в test setup.

```php
// apps/com_my_shop/tests/Pest.php

beforeEach(function () {
    appPackage('com_my_shop');
});

beforeAll(function () {
    $root = dirname(__DIR__, 4);
    $process = new Symfony\Component\Process\Process(
        ['php', 'pinoox', 'migrate', 'com_my_shop', '--force'],
        $root
    );
    $process->run();
});
```

---

## Unit — создание и чтение модели

```php
// apps/com_my_shop/tests/Unit/ProductModelTest.php

it('creates a product', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::create([
            'title' => 'Test product',
            'price' => 99000,
        ]);

        expect($product->id)->not->toBeNull()
            ->and($product->title)->toBe('Test product');
    });
});
```

---

## Feature — API с базой данных

```php
it('lists products from database', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'Book',
            'price' => 50000,
        ]);
    });

    $response = appGet(appPackage(), '/api/v1/products');

    $response
        ->assertOk()
        ->assertJsonPath('data.0.title', 'Book');
});
```

---

## Очистка между тестами

Чтобы избежать коллизий данных, очищайте связанные таблицы после каждого теста:

```php
afterEach(function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::query()->delete();
    });
});
```

---

## Тестирование миграций

```php
it('has orders table', function () {
    inApp(appPackage(), function () {
        expect(
            Pinoox\Portal\Database\DB::schema()->hasTable('com_my_shop_order')
        )->toBeTrue();
    });
});
```

Имена таблиц используют префикс пакета (`{package}_`) по соглашению приложения.

---

## Запуск тестов

```bash
php pinoox test com_my_shop -f ProductModel
```

---

## Советы

1. Никогда не запускайте тесты против production БД — используйте `APP_ENV=test` и отдельную БД.
2. Вызывайте seeders в `beforeEach` только когда нужно; предпочитайте создание минимальных данных внутри каждого теста.
3. Тестируйте запросы и связи в **Unit**; полные endpoint — в **Feature**.

---

## Связанные документы

- [Начало работы с тестированием](./getting-started.md)
- [HTTP-тесты](./http-tests.md)
- [Начало работы с базой данных](../database/getting-started.md)
- [Миграции](../database/migrations.md)
- [Eloquent — начало работы](../eloquent-orm/getting-started.md)
- [Factories](../eloquent-orm/factories.md)

---

[← Вернуться к оглавлению](../README.md)
