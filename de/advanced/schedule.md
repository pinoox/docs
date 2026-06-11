# Zeitplanung (Cron)

[← Zurück zur Übersicht](../README.md)

Verwenden Sie Pinoox **Schedule** für wiederkehrende Aufgaben (Cache-Bereinigung, nächtliche Reports, Datensynchronisation). Tasks werden pro App in `schedule.php` oder `boot.php` registriert.

---

## Schedule-Datei der App

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
        // PHP-Logik
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## Registrierung über boot.php

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

## Task-Typen

| Typ | Beispiel |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### Häufige Intervalle

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

Optionen: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Server-Crontab

Den Scheduler jede Minute ausführen:

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

## Verwandte Dokumente

- [boot.php & Events](./boot-and-events.md)
- [CLI-Referenz](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← Zurück zur Übersicht](../README.md)
