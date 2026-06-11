# Планирование задач (cron)

[← Назад к оглавлению](../README.md)

Используйте **Schedule** в Pinoox для регулярных задач (очистка кэша, ночные отчёты, синхронизация данных). Задачи регистрируются для каждого приложения в `schedule.php` или `boot.php`.

---

## Файл расписания приложения

```text
apps/{package}/schedule.php
```

```php
<?php

use Pinoox\Cron\Schedule;

return function (Schedule $schedule): void {
    $schedule->command('cache:clear')
        ->dailyAt('02:00')
        ->name('clear-cache')
        ->description('Clear runtime cache')
        ->withoutOverlapping();

    $schedule->call(function () {
        // PHP-логика
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## Регистрация из boot.php

```php
use Pinoox\Component\AppEvent\AppRegister;

return function (AppRegister $register): void {
    $register->schedule(function ($schedule) {
        $schedule->command('pinker:rebuild')
            ->weekly()
            ->name('rebuild-pinker');
    });
};
```

---

## Типы задач

| Тип | Пример |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### Часто используемые периодичности

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

Опции: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Crontab на сервере

Запускайте планировщик каждую минуту:

```bash
* * * * * cd /path/to/pinoox && php pinoox schedule:run >> /dev/null 2>&1
```

---

## CLI

```bash
php pinoox schedule:list
php pinoox schedule:list com_acme_shop
php pinoox schedule:run
```

---

## Связанные документы

- [boot.php и события](./boot-and-events.md)
- [Справочник CLI](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← Назад к оглавлению](../README.md)
