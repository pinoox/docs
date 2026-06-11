# スケジューリング（cron）

[← 索引に戻る](../README.md)

Pinoox **Schedule** で定期作業（Cache クリーンアップ、夜間レポート、データ同期）を実行します。タスクは `schedule.php` または `boot.php` でアプリごとに登録します。

---

## アプリ schedule ファイル

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
        // PHP ロジック
    })
        ->hourly()
        ->name('heartbeat');
};
```

---

## boot.php から登録

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

## タスクタイプ

| タイプ | 例 |
|------|---------|
| **command** | `$schedule->command('migrate com_acme_shop')` |
| **shell** | `$schedule->shell('backup.sh')` |
| **call** | `$schedule->call(fn () => …)` |

### よく使う頻度

```php
->everyMinute()
->hourly()
->dailyAt('02:30')
->weekly()
->monthly()
->cron('0 */6 * * *')
```

オプション: `->withoutOverlapping()`、`->name()`、`->description()`。

---

## サーバー crontab

スケジューラを毎分実行:

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

## 関連ドキュメント

- [boot.php とイベント](./boot-and-events.md)
- [CLI リファレンス](../start/cli-reference.md)
- [Pinker](./pinker.md)

---

[← 索引に戻る](../README.md)
