# اختبار Console في Pinoox

[← العودة إلى الفهرس](../README.md)

لاختبار أوامر CLI في Pinoox (`php pinoox ...`)، استخدم `Symfony\Component\Process\Process` في اختبارات Pest. أكّد المخرجات ورموز الخروج — الأسلوب الموصى به لاختبار الطرفية.

---

## المتطلبات المسبقة

Symfony Console Process متاح بالفعل في تبعيات المشروع. اكتب الاختبارات في مجلدات `Feature` أو `Unit` للتطبيق أو النواة.

---

## اختبار أمر migrate

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

## اختبار أمر تطبيق مخصص

أوامر التطبيق في `apps/{package}/Terminal/` وتُكتشف عبر `php pinoox`:

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

## اختبار خروج الفشل

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

## الأوامر التفاعلية — تجنّبها

للأوامر التي تطلب إدخال المستخدم، مرّر كل المعاملات في الاختبارات حتى لا تعمل تفاعليًا:

```bash
# ✅ in tests
php pinoox migrate com_my_shop

# ❌ in tests — waits for user selection
php pinoox migrate
```

---

## تشغيل الاختبارات

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## نصائح

1. وجّه `$root` إلى جذر المشروع (حيث `pinoox` و`index.php`).
2. اضبط timeout طويلًا لـ migrate في CI: `$process->setTimeout(120)`.
3. للمنطق الخالص داخل فئة Command، استخدم **Unit test** مع تبعيات mock؛ Process لتكامل CLI end-to-end فقط.

---

## وثائق ذات صلة

- [البدء مع الاختبار](./getting-started.md)
- [Mocking](./mocking.md)
- [الترحيلات (Migrations)](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [بنية المشروع](../start/structure.md)

---

[← العودة إلى الفهرس](../README.md)
