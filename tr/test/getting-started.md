# Pinoox'ta teste başlarken

[← Dizine dön](../README.md)

Pinoox **framework çekirdeği** (`tests/`) ve **her uygulama** (`apps/{package}/tests/`) için tek bir yaklaşım kullanır: [Pest](https://pestphp.com/), paylaşılan bootstrap ve `AppTestKit`. Bu rehber pratik örneklerle bu standart iş akışını adım adım açıklar.

---

## Test yığını

| Araç | Rol |
|------|------|
| Pest | PHP testlerini çalıştırma |
| `Pinoox\Component\Test\AppTestKit` | Ortam boot, geçici uygulama, HTTP istekleri |
| `tests/bootstrap.php` | Çekirdek ve uygulama testleri için paylaşılan giriş noktası |

---

## Testleri çalıştırma

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

CI'da `composer.json` içindeki script'leri de kullanabilirsiniz:

```bash
composer test          # platform tests
composer test:apps     # all app tests
```

---

## Uygulama test klasör yapısı

`php pinoox app:create` çalıştırıldığında `tests/` klasörü otomatik oluşturulur:

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

Yeni test oluşturma:

```bash
php pinoox test:create OrderTest com_my_shop
php pinoox test:create PriceCalculatorTest com_my_shop --unit
```

---

## `tests/Pest.php` dosyası

```php
require dirname(__DIR__, 3) . '/tests/bootstrap.php';

uses(Tests\AppTestCase::class)->in('Feature', 'Unit');

beforeEach(function () {
    appPackage('com_my_shop');
});
```

`appPackage()` helper'ı aktif uygulamayı helper'lar ve otomatik algılama için ayarlar.

---

## Global helper'lar

| Helper | Amaç |
|--------|---------|
| `appPackage($package?)` | Aktif paketi ayarla / oku |
| `inApp($package, fn)` | `App::meeting()` içinde kod çalıştır |
| `appPath($package, $sub = '')` | Uygulama klasörü yolu |
| `fakeApp($package, $files)` | Özel dosyalarla geçici uygulama oluştur |
| `deleteFakeApp($package)` | Geçici uygulamayı kaldır |
| `appGet($package, $uri, ...)` | GET isteği → `TestResponse` |
| `appPost($package, $uri, $data)` | POST isteği |
| `appPostJson($package, $uri, $json)` | JSON POST isteği |
| `pinooxBoot()` | Test ortamını boot et |

---

## Unit — Component sınıfını test etme

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

## Feature — uygulama boot smoke testi

```php
it('boots the app', function () {
    $package = appPackage();

    inApp($package, function () use ($package) {
        expect(Pinoox\Portal\App\AppEngine::exists($package))->toBeTrue();
    });
});
```

---

## Çekirdek ve uygulama

| Konum | Amaç | Temel sınıf |
|----------|---------|-----------|
| `tests/Feature/` | Framework, portal'lar, router | `Tests\TestCase` |
| `apps/{pkg}/tests/Feature/` | HTTP, Flow, entegrasyon | `Tests\AppTestCase` |
| `apps/{pkg}/tests/Unit/` | Component, saf mantık | `Tests\AppTestCase` |

---

## Test modu

Test ortamında `mode` otomatik olarak `test` olarak ayarlanır:

```php
config('~pinoox')->get('mode'); // 'test'
```

CI'da gerektiğinde `.env.testing` veya `APP_ENV=test` yapılandırın.

---

## İpuçları

1. `fakeApp()` sonrası `afterEach` içinde her zaman `deleteFakeApp()` çağırın.
2. Uygulama içinde config, portal veya model için `inApp()` kullanın.
3. Route ve API'ler için `appGet` / `appPostJson` kullanın.
4. Route'lar → **Feature**; `Component/` sınıfları → **Unit**.
5. Dosyaları elle kopyalamak yerine `php pinoox test:create` kullanın.

---

## İlgili dokümantasyon

- [HTTP testleri](./http-tests.md)
- [Konsol testleri](./console-tests.md)
- [Tarayıcı (HTML) testleri](./browser-tests.md)
- [Veritabanı testleri](./database.md)
- [Mocking](./mocking.md)
- [İlk uygulamanız](../start/your-first-app.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)
