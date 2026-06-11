# Console Testing in Pinoox

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox CLI commands (`php pinoox ...`) test करने के लिए Pest tests में `Symfony\Component\Process\Process` उपयोग करें। Output और exit codes assert करें — terminal testing के लिए recommended approach।

---

## Prerequisites

Symfony Console Process project dependencies में पहले से available है। App या core `Feature` या `Unit` folders में tests लिखें।

---

## migrate command test

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

## Custom app command test

App commands `apps/{package}/Terminal/` में रहते हैं और `php pinoox` के ज़रिए discover होते हैं:

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

## Failure exit test

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

## Interactive commands — avoid them

User input prompt करने वाले commands के लिए tests में full arguments pass करें ताकि interactively न चलें:

```bash
# ✅ in tests
php pinoox migrate com_my_shop

# ❌ in tests — waits for user selection
php pinoox migrate
```

---

## Running tests

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## Tips

1. `$root` project root पर point करे (जहाँ `pinoox` और `index.php` हैं)।
2. CI में migrate के लिए long timeout set करें: `$process->setTimeout(120)`.
3. Command class के अंदर pure logic के लिए mocked dependencies के साथ **Unit test**; Process केवल end-to-end CLI integration के लिए।

---

## संबंधित docs

- [Getting started with testing](./getting-started.md)
- [Mocking](./mocking.md)
- [Migrations](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Project structure](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
