# Console Testing in Pinoox

To test Pinoox CLI commands (`php pinoox ...`), use `Symfony\Component\Process\Process` in Pest tests. Assert output and exit codes — the recommended approach for terminal testing.

---

## Prerequisites

Symfony Console Process is already available in project dependencies. Write tests in app or core `Feature` or `Unit` folders.

---

## Testing the migrate command

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

## Testing a custom app command

App commands live in `apps/{package}/Terminal/` and are discovered via `php pinoox`:

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

## Testing failure exit

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

For commands that prompt for user input, pass full arguments in tests so they do not run interactively:

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

1. Point `$root` at the project root (where `pinoox` and `index.php` live).
2. Set a long timeout for migrate in CI: `$process->setTimeout(120)`.
3. For pure logic inside a Command class, use a **Unit test** with mocked dependencies; Process is for end-to-end CLI integration only.

---

## Related docs

- [Getting started with testing](./getting-started.md)
- [Mocking](./mocking.md)
- [Migrations](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Project structure](../start/structure.md)
