# Zamanlama (cron)

[← Dizine dön](../README.md)

Tekrarlayan işler (önbellek temizliği, gece raporları, veri senkronizasyonu) için Pinoox **Schedule** kullanın. Görevler uygulama başına `schedule.php` veya `boot.php` içinde kaydedilir.

---

## Uygulama zamanlama dosyası

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

## boot.php'den kaydetme

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

## Görev türleri

| Tür | Örnek |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### Yaygın sıklıklar

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

Seçenekler: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Sunucu crontab

Zamanlayıcıyı her dakika çalıştırın:

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

## İlgili dokümantasyon

- [boot.php ve event'ler](./boot-and-events.md)
- [CLI referansı](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← Dizine dön](../README.md)
