# Pinoox Serialization 테스트

[← 색인으로 돌아가기](../README.md)

API와 Resource는 `TestResponse`의 `assertJsonPath()`, `json()`으로 JSON output 검사. Eloquent model은 `inApp()` 내부에서 `toArray()` / `toJson()` assert.

---

## API — envelope 구조

Pinoox API response는 보통 `success`, `data` field 포함:

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

## JSON 일부 읽기

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — created resource response

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

Model에 `$hidden` field와 cast 정의 — model에서 설정, test에서 assert.

---

## Unit — JSON string

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

## Test 실행

```bash
php pinoox test com_my_shop -f Serialization
```

---

## 관련 문서

- [HTTP tests](./http-tests.md)
- [Responses](../basic/responses.md)
- [Eloquent — serialization](../eloquent-orm/serialization.md)
- [API resources](../eloquent-orm/api-resources.md)
- [Mutators / casts](../eloquent-orm/mutators-casts.md)

---

[← 색인으로 돌아가기](../README.md)
