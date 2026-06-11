# Pinoox Console 테스트

[← 색인으로 돌아가기](../README.md)

Pinoox CLI command(`php pinoox ...`) 테스트에는 Pest test에서 `Symfony\Component\Process\Process` 사용. output과 exit code assert — terminal test 권장 방법.

---

## Prerequisites

Symfony Console Process는 프로젝트 dependency에 이미 포함. app 또는 core `Feature` / `Unit` 폴더에 test 작성.

---

## migrate command 테스트

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

## custom app command 테스트

App command는 `apps/{package}/Terminal/`에 있으며 `php pinoox`로 discover:

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

## failure exit 테스트

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

## Interactive command — 피하기

user input을 prompt하는 command는 test에서 full argument 전달:

```bash
# ✅ in tests
php pinoox migrate com_my_shop

# ❌ in tests — waits for user selection
php pinoox migrate
```

---

## Test 실행

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## Tips

1. `$root`를 프로젝트 루트(`pinoox`, `index.php` 위치)로 지정.
2. CI migrate에 긴 timeout: `$process->setTimeout(120)`.
3. Command class 내부 pure logic은 mock dependency **Unit test**; Process는 end-to-end CLI integration 전용.

---

## 관련 문서

- [테스트 시작하기](./getting-started.md)
- [Mocking](./mocking.md)
- [Migrations](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)
