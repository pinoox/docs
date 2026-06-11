# 计划任务（Scheduling / cron）

[← 返回索引](../README.md)

使用 Pinoox 的 **Schedule** 处理周期性工作（缓存清理、夜间报表、数据同步）。任务按应用注册在 `schedule.php` 或 `boot.php` 中。

---

## 应用的 schedule 文件

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
        // PHP 逻辑
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## 从 boot.php 注册

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

## 任务类型

| 类型 | 示例 |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### 常用频率

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

选项：`->withoutOverlapping()`、`->name()`、`->description()`。

---

## 服务器 crontab

每分钟运行一次调度器：

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

## 相关文档

- [boot.php 与事件](./boot-and-events.md)
- [CLI 参考](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← 返回索引](../README.md)
