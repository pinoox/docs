# الجدولة (Cron)

[← العودة إلى الفهرس](../README.md)

استخدم **Schedule** في Pinoox للمهام المتكررة (تنظيف التخزين المؤقت، التقارير الليلية، مزامنة البيانات). تُسجَّل المهام لكل تطبيق في `schedule.php` أو `boot.php`.

---

## ملف الجدولة الخاص بالتطبيق

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
        // منطق PHP
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## التسجيل من boot.php

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

## أنواع المهام

| النوع | مثال |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### التكرارات الشائعة

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

الخيارات: `->withoutOverlapping()`، `->name()`، `->description()`.

---

## crontab على الخادم

شغّل المجدول كل دقيقة:

```bash
* * * * * cd /path/to/pinoox && php pinoox schedule:run >> /dev/null 2>&1
```

---

## سطر الأوامر (CLI)

```bash
php pinoox schedule:list
php pinoox schedule:list com_acme_shop
php pinoox schedule:run
```

---

## وثائق ذات صلة

- [boot.php والأحداث](./boot-and-events.md)
- [مرجع سطر الأوامر (CLI)](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← العودة إلى الفهرس](../README.md)
