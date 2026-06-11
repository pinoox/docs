# Pinoox 테스트 시작하기

[← 색인으로 돌아가기](../README.md)

Pinoox는 **프레임워크 코어**(`tests/`)와 **각 앱**(`apps/{package}/tests/`)에 동일한 접근을 사용합니다: [Pest](https://pestphp.com/), 공유 bootstrap, `AppTestKit`. 이 가이드는 실용 예제와 함께 표준 workflow를 안내합니다.

---

## Test stack

| Tool | Role |
|------|------|
| Pest | PHP test 실행 |
| `Pinoox\Component\Test\AppTestKit` | 환경 boot, 임시 앱, HTTP request |
| `tests/bootstrap.php` | core와 app test 공유 진입점 |

---

## Test 실행

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

CI에서는 `composer.json` script 사용 가능:

```bash
composer test          # platform tests
composer test:apps     # all app tests
```

---

## App test 폴더 구조

`php pinoox app:create` 실행 시 `tests/` 폴더 자동 생성:

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

새 test 생성:

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

`appPackage()` helper가 helper와 auto-detection용 활성 앱 설정.

---

## Global helper

| Helper | Purpose |
|--------|---------|
| `appPackage($package?)` | 활성 package 설정 / 읽기 |
| `inApp($package, fn)` | `App::meeting()` 내부에서 code 실행 |
| `appPath($package, $sub = '')` | 앱 폴더 경로 |
| `fakeApp($package, $files)` | custom file로 임시 앱 생성 |
| `deleteFakeApp($package)` | 임시 앱 제거 |
| `appGet($package, $uri, ...)` | GET request → `TestResponse` |
| `appPost($package, $uri, $data)` | POST request |
| `appPostJson($package, $uri, $json)` | JSON POST request |
| `pinooxBoot()` | test 환경 boot |

---

## Unit — Component class 테스트

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

## Feature — 앱 boot smoke test

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
| `tests/Feature/` | Framework, portal, router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, integration | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, pure logic | `Tests\AppTestCase` |

---

## Test mode

test 환경에서 `mode`는 자동으로 `test`:

```php
config('~pinoox')->get('mode'); // 'test'
```

CI에서는 필요 시 `.env.testing` 또는 `APP_ENV=test` 설정.

---

## Tips

1. `fakeApp()` 후 `afterEach`에서 항상 `deleteFakeApp()` 호출.
2. 앱 내부 config, portal, model에는 `inApp()` 사용.
3. route와 API에는 `appGet` / `appPostJson` 사용.
4. Route → **Feature**; `Component/` class → **Unit**.
5. file 수동 복사 대신 `php pinoox test:create` 사용.

---

## 관련 문서

- [HTTP tests](./http-tests.md)
- [Console tests](./console-tests.md)
- [Browser (HTML) tests](./browser-tests.md)
- [Database tests](./database.md)
- [Mocking](./mocking.md)
- [첫 번째 앱 만들기](../start/your-first-app.md)
- [프로젝트 구조](../start/structure.md)

---

[← 색인으로 돌아가기](../README.md)
