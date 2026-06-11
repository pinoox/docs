# शेड्यूलिंग (cron)

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

बार-बार होने वाले कामों (cache cleanup, रात्रिकालीन रिपोर्ट, data sync) के लिए Pinoox **Schedule** का उपयोग करें। Tasks प्रत्येक ऐप के लिए `schedule.php` या `boot.php` में रजिस्टर किए जाते हैं।

---

## ऐप schedule फ़ाइल

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
        // PHP लॉजिक
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## boot.php से रजिस्टर करना

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

## Task के प्रकार

| प्रकार | उदाहरण |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### सामान्य आवृत्तियाँ (frequencies)

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

विकल्प: `->withoutOverlapping()`, `->name()`, `->description()`।

---

## सर्वर crontab

Scheduler को हर मिनट चलाएँ:

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

## संबंधित दस्तावेज़

- [boot.php & events](./boot-and-events.md)
- [CLI reference](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)
