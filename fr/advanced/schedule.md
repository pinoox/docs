# Planification (cron)

[← Retour à l'index](../README.md)

Utilisez **Schedule** de Pinoox pour les tâches récurrentes (nettoyage de cache, rapports nocturnes, synchronisation de données). Les tâches sont enregistrées par application dans `schedule.php` ou `boot.php`.

---

## Fichier de planification de l'application

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
        // logique PHP
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## Enregistrer depuis boot.php

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

## Types de tâches

| Type | Exemple |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### Fréquences courantes

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

Options : `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Crontab du serveur

Exécutez le planificateur chaque minute :

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

## Documentation associée

- [boot.php et événements](./boot-and-events.md)
- [Référence CLI](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← Retour à l'index](../README.md)
