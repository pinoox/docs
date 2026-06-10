# Getting Started with Testing in Pinoox

[← Back to index](../../readme.md)

Pinoox uses a single approach for the **framework core** (`tests/`) and **each app** (`apps/{package}/tests/`): [Pest](https://pestphp.com/), a shared bootstrap, and `AppTestKit`. This guide walks through that standard workflow with practical examples.

---

## Test stack

| Tool | Role |
|------|------|
| Pest | Running PHP tests |
| `Pinoox\Component\Test\AppTestKit` | Boot environment, temporary app, HTTP requests |
| `tests/bootstrap.php` | Shared entry point for core and app tests |

---

## Running tests

```bash
# All core tests
vendor/bin/pest

# From CLI (interactive package selection)
php pinoox test

# A specific app
php pinoox test com_my_shop

# Filter by test name
php pinoox test com_my_shop -f login
php pinoox test -f UserSystem

# Feature or Unit only
php pinoox test com_my_shop --feature
php pinoox test com_my_shop --unit
```

In CI you can also use the scripts in `composer.json`:

```bash
composer test          # platform tests
composer test:apps     # all app tests
```

---

## App test folder structure

Running `php pinoox app:create` creates the `tests/` folder automatically:

```
apps/com_my_shop/
├── app.php
├── Controller/
├── routes/
└── tests/
    ├── Pest.php              ← bootstrap + AppTestCase
    ├── Feature/
    │   └── AppBootTest.php   ← smoke test
    └── Unit/
```

Create a new test:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## The `tests/Pest.php` file

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

The `appPackage()` helper sets the active app for helpers and auto-detection.

---

## Global helpers

| Helper | Purpose |
|--------|---------|
| `appPackage($package?)` | Set / read the active package |
| `inApp($package, fn)` | Run code inside `App::meeting()` |
| `appPath($package, $sub = '')` | Path to the app folder |
| `fakeApp($package, $files)` | Create a temporary app with custom files |
| `deleteFakeApp($package)` | Remove a temporary app |
| `appGet($package, $uri, ...)` | GET request → `TestResponse` |
| `appPost($package, $uri, $data)` | POST request |
| `appPostJson($package, $uri, $json)` | JSON POST request |
| `pinooxBoot()` | Boot the test environment |

---

## Unit — testing a Component class

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

## Feature — app boot smoke test

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## Core vs app

| Location | Purpose | Base case |
|----------|---------|-----------|
| `tests/Feature/` | Framework, portals, router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, integration | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, pure logic | `Tests\AppTestCase` |

---

## Test mode

In the test environment, `mode` is automatically set to `test`:

```php
config('~pinoox')->get('mode'); // 'test'
```

In CI, configure `.env.testing` or `APP_ENV=test` when needed.

---

## Tips

1. After `fakeApp()`, always call `deleteFakeApp()` in `afterEach`.
2. Use `inApp()` for config, portals, or models inside an app.
3. Use `appGet` / `appPostJson` for routes and APIs.
4. Routes → **Feature**; `Component/` classes → **Unit**.
5. Use `php pinoox test:create` instead of copying files by hand.

---

## Related docs

- [HTTP tests](./http-tests.md)
- [Console tests](./console-tests.md)
- [Browser (HTML) tests](./browser-tests.md)
- [Database tests](./database.md)
- [Mocking](./mocking.md)
- [Your first app](../start/your-first-app.md)
- [Project structure](../start/structure.md)

---

[← Back to index](../../readme.md)
