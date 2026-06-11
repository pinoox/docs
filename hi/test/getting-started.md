# Getting Started with Testing in Pinoox

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox **framework core** (`tests/`) और **हर app** (`apps/{package}/tests/`) के लिए single approach उपयोग करता है: [Pest](https://pestphp.com/), shared bootstrap, और `AppTestKit`। यह guide practical examples के साथ standard workflow walkthrough करती है।

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

CI में `composer.json` scripts भी उपयोग कर सकते हैं:

```bash
composer test          # platform tests
composer test:apps     # all app tests
```

---

## App test folder structure

`php pinoox app:create` चलाने पर `tests/` folder automatically create होता है:

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

नया test बनाएँ:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## `tests/Pest.php` file

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

`appPackage()` helper active app set करता है helpers और auto-detection के लिए।

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

## Unit — Component class test

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

Test environment में `mode` automatically `test` set होता है:

```php
config('~pinoox')->get('mode'); // 'test'
```

CI में ज़रूरत हो तो `.env.testing` या `APP_ENV=test` configure करें।

---

## Tips

1. `fakeApp()` के बाद `afterEach` में हमेशा `deleteFakeApp()` call करें।
2. App के अंदर config, portals, या models के लिए `inApp()` उपयोग करें।
3. Routes और APIs के लिए `appGet` / `appPostJson` उपयोग करें।
4. Routes → **Feature**; `Component/` classes → **Unit**।
5. Files manually copy करने की जगह `php pinoox test:create` उपयोग करें।

---

## संबंधित docs

- [HTTP tests](./http-tests.md)
- [Console tests](./console-tests.md)
- [Browser (HTML) tests](./browser-tests.md)
- [Database tests](./database.md)
- [Mocking](./mocking.md)
- [Your first app](../start/your-first-app.md)
- [Project structure](../start/structure.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
