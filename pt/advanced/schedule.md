# Agendamento (cron)

[← Voltar ao índice](../README.md)

Use o **Schedule** do Pinoox para trabalhos recorrentes (limpeza de cache, relatórios noturnos, sincronização de dados). As tarefas são registradas por app no `schedule.php` ou no `boot.php`.

---

## Arquivo de schedule do app

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
        // lógica PHP
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## Registrar a partir do boot.php

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

## Tipos de tarefa

| Tipo | Exemplo |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### Frequências comuns

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

Opções: `->withoutOverlapping()`, `->name()`, `->description()`.

---

## Crontab do servidor

Execute o scheduler a cada minuto:

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

## Documentação relacionada

- [boot.php e events](./boot-and-events.md)
- [Referência da CLI](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← Voltar ao índice](../README.md)
