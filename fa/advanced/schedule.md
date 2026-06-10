# زمان‌بندی (Schedule / Cron)

[← بازگشت به فهرست](../../readme-fa.md)

برای کارهای تکراری (پاک‌سازی cache، گزارش شبانه، sync داده) از **Schedule** پینوکس استفاده کنید. API شبیه task schedulerهای رایج PHP است، اما وظایف per-app در `schedule.php` یا `boot.php` ثبت می‌شوند.

---

## فایل schedule اپ

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
        ->description('پاک‌سازی cache')
        ->withoutOverlapping();

    $schedule->call(function () {
        // منطق PHP
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## ثبت از boot.php

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

## انواع task

| نوع | مثال |
|-----|------|
| **command** | `$schedule->command('migrate com_acme_shop')` → `php pinoox …` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### فرکانس‌های رایج

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

گزینه‌ها: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Cron سرور

هر دقیقه scheduler را صدا بزنید (مسیر پروژه را تنظیم کنید):

```bash
* * * * * cd /path/to/pinoox && php pinoox schedule:run >> /dev/null 2>&1
```

---

## CLI

```bash
# لیست taskها
php pinoox schedule:list

# فقط یک اپ
php pinoox schedule:list com_acme_shop

# اجرای taskهای due (همان چیزی که cron صدا می‌زند)
php pinoox schedule:run
```

---

## مستندات مرتبط

- [boot.php و رویدادها](./boot-and-events.md)
- [CLI — خط فرمان](../start/cli-reference.md)
- [Pinker — بیلد](./pinker.md)

---

[← بازگشت به فهرست](../../readme-fa.md)
