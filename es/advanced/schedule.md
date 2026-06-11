# Programación (cron)

[← Volver al índice](../README.md)

Usa **Schedule** de Pinoox para trabajo recurrente (limpieza de caché, informes nocturnos, sincronización de datos). Las tareas se registran por app en `schedule.php` o `boot.php`.

---

## Archivo schedule de la app

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

## Registrar desde boot.php

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

## Tipos de tarea

| Tipo | Ejemplo |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### Frecuencias comunes

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

Opciones: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Crontab del servidor

Ejecuta el planificador cada minuto:

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

## Documentación relacionada

- [boot.php y eventos](./boot-and-events.md)
- [Referencia CLI](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← Volver al índice](../README.md)
