# Scheduling (cron)

[← Back to index](../../readme.md)

Use Pinoox **Schedule** for recurring work (cache cleanup, nightly reports, data sync). Tasks are registered per app in `schedule.php` or `boot.php`.

---

## App schedule file

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
        // PHP logic
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## Register from boot.php

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

## Task types

| Type | Example |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### Common frequencies

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

Options: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Server crontab

Run the scheduler every minute:

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

## Related docs

- [boot.php & events](./boot-and-events.md)
- [CLI reference](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← Back to index](../../readme.md)
