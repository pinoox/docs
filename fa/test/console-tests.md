# تست Console در پینوکس

[← بازگشت به فهرست](../README.md)

برای تست دستورات CLI پینوکس (`php pinoox ...`) از `Symfony\Component\Process\Process` در تست Pest استفاده کنید. خروجی و کد خروج را assert می‌کنید — همان روش پیشنهادی برای تست ترمینال.

---

## پیش‌نیاز

Process در Symfony Console از قبل در وابستگی‌های پروژه موجود است. تست را در `Feature` یا `Unit` اپ یا هسته بنویسید.

---

## تست دستور migrate

```php
// apps/com_my_shop/tests/Feature/MigrateCommandTest.php

use Symfony\Component\Process\Process;

it('runs migrate for the app', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', appPackage()],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeTrue()
        ->and($process->getOutput())->toContain('Migrated');
});
```

---

## تست دستور سفارشی اپ

دستورهای اپ در `apps/{package}/Terminal/` قرار می‌گیرند و با `php pinoox` کشف می‌شوند:

```php
it('runs custom report command', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'report:daily', '-p', appPackage()],
        $root
    );

    $process->run();

    expect($process->getExitCode())->toBe(0);
});
```

---

## تست خروج با خطا

```php
it('fails when package is missing', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', 'com_nonexistent'],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeFalse();
});
```

---

## تست تعاملی — اجتناب

دستوراتی که از کاربر ورودی می‌خواهند را در تست با آرگومان کامل صدا بزنید تا interactive نشود:

```bash
# ✅ در تست
php pinoox migrate com_my_shop
php pinoox user:list com_my_shop --json

# ❌ در تست — منتظر انتخاب کاربر می‌ماند
php pinoox migrate
php pinoox user:list
```

---

## تست ایزوله CLI (pincore)

تست‌های Pest در `pincore/tests/Feature/Cli/` برای دستورات `pincore/Terminal/`:

| موضوع | فایل |
|--------|------|
| همه دستورات | `CliRegistryTest.php` |
| DB | `DatabaseCliTest.php` |
| کاربر / نقش / permission | `UserCliTest.php`, `RoleCliTest.php`, `PermissionCliTest.php` |
| توکن / فایل / query | `TokenCliTest.php`, `FileCliTest.php`, `QueryCliTest.php` |

از `CommandTester`، trait probe و sqlite `:memory:` استفاده می‌شود.

```bash
php vendor/bin/pest --testsuite=Cli --configuration=pincore/phpunit.xml
```

Helper: `pincore/tests/Support/CliTestHelpers.php`.

---

## اجرا

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## نکات

1. `$root` را به ریشه پروژه (جایی که `pinoox` و `index.php` هست) اشاره دهید.
2. timeout طولانی برای migrate در CI: `$process->setTimeout(120)`.
3. برای منطق pure داخل کلاس Command از **Unit test** با mock وابستگی‌ها استفاده کنید؛ Process فقط برای یکپارچگی end-to-end CLI مناسب است.

---

## مستندات مرتبط

- [شروع تست در پینوکس](./getting-started.md)
- [Mocking — شبیه‌سازی](./mocking.md)
- [Migration — مهاجرت](../database/migrations.md)
- [Pinker — بیلد Pinoox](../advanced/pinker.md)
- [ساختار پوشه‌بندی](../start/structure.md)

---

[← بازگشت به فهرست](../README.md)
