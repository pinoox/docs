# Pinoox での Serialization テスト

[← 索引に戻る](../README.md)

API と Resource では `TestResponse` の `assertJsonPath()` と `json()` で JSON 出力を検査します。Eloquent Model では `inApp()` 内で `toArray()` / `toJson()` をアサートします。

---

## API — エンベロープ構造

Pinoox API レスポンスは通常 `success` と `data` フィールドを含みます:

```php
// apps/com_my_shop/tests/Feature/ProductSerializationTest.php

it('serializes product in api envelope', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'Mouse',
            'price' => 250000,
        ]);
    });

    $response = appGet(appPackage(), '/api/v1/products/1');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'Mouse')
        ->assertJsonPath('data.price', 250000);
});
```

---

## JSON の一部を読み取り

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — 作成リソースレスポンス

```php
it('returns created resource', function () {
    $response = appPostJson(appPackage(), '/api/v1/products', [
        'title' => 'Keyboard',
        'price' => 1800000,
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.title', 'Keyboard')
        ->and($response->json('data.id'))->toBeInt();
});
```

---

## Unit — model toArray

```php
// apps/com_my_shop/tests/Unit/ProductArrayTest.php

it('hides internal fields in array', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::make([
            'title' => 'Test',
            'internal_note' => 'confidential',
        ]);

        $array = $product->toArray();

        expect($array)->toHaveKey('title')
            ->and($array)->not->toHaveKey('internal_note');
    });
});
```

Model で `$hidden` フィールドと cast を定義 — そこで設定し、テストでアサート。

---

## Unit — JSON 文字列

```php
it('encodes to valid json', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::make(['title' => 'A']);

        $json = $product->toJson();
        $decoded = json_decode($json, true);

        expect($decoded['title'])->toBe('A');
    });
});
```

---

## テストの実行

```bash
php pinoox test com_my_shop -f Serialization
```

---

## 関連ドキュメント

- [HTTP テスト](./http-tests.md)
- [Response](../basic/responses.md)
- [Eloquent Serialization](../eloquent-orm/serialization.md)
- [API Resources](../eloquent-orm/api-resources.md)
- [Mutators / Casts](../eloquent-orm/mutators-casts.md)

---

[← 索引に戻る](../README.md)
