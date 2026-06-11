# Scheduling (cron)

[← 색인으로 돌아가기](../README.md)

반복 작업(cache 정리, nightly report, data sync)에는 Pinoox **Schedule**을 사용하세요. Task는 앱당 `schedule.php` 또는 `boot.php`에 등록합니다.

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

## boot.php에서 등록

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

## Task 유형

| Type | Example |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### 자주 쓰는 frequency

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

옵션: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Server crontab

매분 scheduler 실행:

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

## 관련 문서

- [boot.php & events](./boot-and-events.md)
- [CLI reference](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← 색인으로 돌아가기](../README.md)
