# Тестирование консоли в Pinoox

[← Вернуться к оглавлению](../README.md)

Для тестирования CLI-команд Pinoox (`php pinoox ...`) используйте `Symfony\Component\Process\Process` в Pest-тестах. Проверяйте вывод и коды выхода — рекомендуемый подход для тестирования терминала.

---

## Предварительные условия

Symfony Console Process уже доступен в зависимостях проекта. Пишите тесты в папках `Feature` или `Unit` приложения или ядра.

---

## Тестирование команды migrate

```php
// apps/com_my_shop/tests/Feature/MigrateCommandTest.php

use Symfony\Component\Process\Process;

it('runs migrate for the app', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', appPackage()],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeTrue()
        ->and($process->getOutput())->toContain('Migrated');
});
```

---

## Тестирование пользовательской команды приложения

Команды приложения находятся в `apps/{package}/Terminal/` и обнаруживаются через `php pinoox`:

```php
it('runs custom report command', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'report:daily', '-p', appPackage()],
        $root
    );

    $process->run();

    expect($process->getExitCode())->toBe(0);
});
```

---

## Тестирование неуспешного выхода

```php
it('fails when package is missing', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', 'com_nonexistent'],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeFalse();
});
```

---

## Интерактивные команды — избегайте их

Для команд, запрашивающих ввод пользователя, передавайте полные аргументы в тестах, чтобы они не выполнялись интерактивно:

```bash
# ✅ в тестах
php pinoox migrate com_my_shop

# ❌ в тестах — ждёт выбор пользователя
php pinoox migrate
```

---

## Запуск тестов

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## Советы

1. Указывайте `$root` на корень проекта (где находятся `pinoox` и `index.php`).
2. Установите длинный timeout для migrate в CI: `$process->setTimeout(120)`.
3. Для чистой логики внутри класса Command используйте **Unit-тест** с замоканными зависимостями; Process — только для end-to-end CLI-интеграции.

---

## Связанные документы

- [Начало работы с тестированием](./getting-started.md)
- [Mocking](./mocking.md)
- [Миграции](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Структура проекта](../start/structure.md)

---

[← Вернуться к оглавлению](../README.md)
