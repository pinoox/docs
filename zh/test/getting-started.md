# Pinoox 测试入门

[← 返回索引](../README.md)

Pinoox 对**框架核心**（`tests/`）和**每个应用**（`apps/{package}/tests/`）采用统一方式：[Pest](https://pestphp.com/)、共享引导文件和 `AppTestKit`。本指南通过实用示例说明该标准工作流。

---

## 测试技术栈

| 工具 | 作用 |
|------|------|
| Pest | 运行 PHP 测试 |
| `Pinoox\Component\Test\AppTestKit` | 引导环境、临时应用、HTTP 请求 |
| `tests/bootstrap.php` | 核心与应用测试的共享入口 |

---

## 运行测试

```bash
# 全部核心测试
vendor/bin/pest

# 通过 CLI（交互选择包）
php pinoox test

# 指定应用
php pinoox test com_my_shop

# 按测试名过滤
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# 仅 Feature 或 Unit
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

在 CI 中也可使用 `composer.json` 中的脚本：

```bash
composer test          # 平台测试
composer test:apps     # 全部应用测试
```

---

## 应用测试目录结构

运行 `php pinoox app:create` 会自动创建 `tests/` 文件夹：

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← 引导 + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← 冒烟测试
    └── Unit/
```

创建新测试：

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## `tests/Pest.php` 文件

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

`appPackage()` 辅助函数为辅助方法与自动检测设置当前活动应用。

---

## 全局辅助函数

| 辅助函数 | 用途 |
|--------|---------|
| `appPackage($package?)` | 设置 / 读取当前包 |
| `inApp($package, fn)` | 在 `App::meeting()` 内运行代码 |
| `appPath($package, $sub = '')` | 应用文件夹路径 |
| `fakeApp($package, $files)` | 用自定义文件创建临时应用 |
| `deleteFakeApp($package)` | 删除临时应用 |
| `appGet($package, $uri, ...)` | GET 请求 → `TestResponse` |
| `appPost($package, $uri, $data)` | POST 请求 |
| `appPostJson($package, $uri, $json)` | JSON POST 请求 |
| `pinooxBoot()` | 引导测试环境 |

---

## Unit — 测试 Component 类

```php
// apps/com_my_shop/tests/Unit/PriceTest.php

it('calculates discount', function () {
    $package = appPackage();

    inApp($package, function () {
        $price = new App\com_my_shop\Component\PriceHelper();
        expect($price->discount(100, 10))->toBe(90);
    });
});
```

---

## Feature — 应用引导冒烟测试

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## 核心与应用

| 位置 | 用途 | 基类 |
|----------|---------|-----------|
| `tests/Feature/` | 框架、门户、路由 | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP、Flow、集成 | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component、纯逻辑 | `Tests\AppTestCase` |

---

## 测试模式

测试环境中 `mode` 会自动设为 `test`：

```php
config('~pinoox')->get('mode'); // 'test'
```

在 CI 中按需配置 `.env.testing` 或 `APP_ENV=test`。

---

## 提示

1. `fakeApp()` 之后务必在 `afterEach` 中调用 `deleteFakeApp()`。
2. 在应用内访问配置、门户或模型时使用 `inApp()`。
3. 路由与 API 使用 `appGet` / `appPostJson`。
4. 路由 → **Feature**；`Component/` 类 → **Unit**。
5. 用 `php pinoox test:create` 代替手动复制文件。

---

## 相关文档

- [HTTP 测试](./http-tests.md)
- [控制台测试](./console-tests.md)
- [浏览器（HTML）测试](./browser-tests.md)
- [数据库测试](./database.md)
- [模拟（Mocking）](./mocking.md)
- [你的第一个应用](../start/your-first-app.md)
- [项目结构](../start/structure.md)

---

[← 返回索引](../README.md)
