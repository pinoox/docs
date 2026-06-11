# Pinoox での Mocking

[← 索引に戻る](../README.md)

テスト対象を外部依存から分離するには **Pest + Mockery**（`mock()`）でクラスを、**`fakeApp()`** で一時アプリをモックします。どちらも Pinoox テスト bootstrap で利用できます。

---

## クラスのモック — Unit

```php
// apps/com_my_shop/tests/Unit/OrderServiceTest.php

use App\com_my_shop\Component\PaymentGateway;
use App\com_my_shop\Component\OrderService;

it('charges via payment gateway', function () {
    $gateway = mock(PaymentGateway::class);
    $gateway->shouldReceive('charge')
        ->once()
        ->with(100000)
        ->andReturn(['status' => 'paid']);

    $service = new OrderService($gateway);

    expect($service->checkout(100000))->toBe(['status' => 'paid']);
});
```

---

## Portal / 静的サービスのモック

Portal が Component に委譲する場合、Component ロジックをテストし Component をモック — Portal を直接モックしない。

```php
it('uses mailer component', function () {
    inApp(appPackage(), function () {
        $mailer = mock(App\com_my_shop\Component\Mailer::class);
        $mailer->shouldReceive('sendWelcome')->once();

        // テスト対象サービスにモックを注入
    });
});
```

---

## fakeApp — 一時アプリ（コアテスト）

実アプリを作らず Router または boot をテスト:

```php
beforeEach(fn () => fakeApp('com_test_shop', [
    'app.php' => '<?php return [
        "package" => "com_test_shop",
        "enable" => true,
        "router" => ["routes" => ["routes/web.php"]],
    ];',
    'routes/web.php' => '<?php use function Pinoox\Router\get; get("/", fn() => "OK");',
]));

afterEach(fn () => deleteFakeApp('com_test_shop'));

it('loads custom routes', function () {
    expect(Pinoox\Portal\App\AppEngine::exists('com_test_shop'))->toBeTrue();
});
```

**重要:** `afterEach` で `deleteFakeApp()` を忘れないこと。

---

## 偽 HTTP — 手動モック不要

実エンドポイントには Controller のモックではなく `appGet` / `appPostJson` を使用 — よりシンプルで本番に近い。

---

## Spy — 呼び出しの検証

```php
$logger = mock(LoggerInterface::class);
$logger->shouldReceive('info')->once()->with('order.created');

// ... コード実行
```

---

## テストの実行

```bash
php pinoox test com_my_shop --unit
php pinoox test com_my_shop -f OrderService
```

---

## ヒント

1. 遅いまたは外部依存（メール、決済、外部 API）のみモック。
2. DB と HTTP 統合ロジックは重いモックではなくテスト Database 付き Feature テストで記述。
3. `fakeApp()` 後は常にクリーンアップ。

---

## 関連ドキュメント

- [テストはじめに](./getting-started.md)
- [HTTP テスト](./http-tests.md)
- [Console テスト](./console-tests.md)
- [Portal](../basic/portal.md)
- [Services](../advanced/services.md)

---

[← 索引に戻る](../README.md)
