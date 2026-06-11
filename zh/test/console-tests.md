# Pinoox 中的控制台测试

[← 返回索引](../README.md)

要测试 Pinoox CLI 命令（`php pinoox ...`），在 Pest 测试中使用 `Symfony\Component\Process\Process`。断言输出与退出码 — 这是终端测试的推荐方式。

---

## 前置条件

Symfony Console Process 已在项目依赖中可用。在应用或核心的 `Feature` 或 `Unit` 文件夹中编写测试。

---

## 测试 migrate 命令

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

## 测试自定义应用命令

应用命令位于 `apps/{package}/Terminal/`，通过 `php pinoox` 发现：

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

## 测试失败退出

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

## 交互式命令 — 避免使用

对会提示用户输入的命令，在测试中传入完整参数，避免交互运行：

```bash
# ✅ 测试中
php pinoox migrate com_my_shop

# ❌ 测试中 — 等待用户选择
php pinoox migrate
```

---

## 运行测试

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## 提示

1. 将 `$root` 指向项目根目录（`pinoox` 与 `index.php` 所在处）。
2. 在 CI 中为 migrate 设置较长超时：`$process->setTimeout(120)`。
3. Command 类内的纯逻辑用**单元测试**并模拟依赖；Process 仅用于端到端 CLI 集成。

---

## 相关文档

- [测试入门](./getting-started.md)
- [模拟（Mocking）](./mocking.md)
- [迁移](../database/migrations.md)
- [Pinoox Baker（Pinker）](../advanced/pinker.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)
