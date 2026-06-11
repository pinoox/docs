# Pinoox 中的序列化测试

[← 返回索引](../README.md)

对 API 与 Resource，在 `TestResponse` 上用 `assertJsonPath()` 和 `json()` 检查 JSON 输出。对 Eloquent 模型，在 `inApp()` 内断言 `toArray()` / `toJson()`。

---

## API — 信封结构

Pinoox API 响应通常包含 `success` 和 `data` 字段：

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

## 读取 JSON 片段

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — 已创建资源响应

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

## Unit — 模型 toArray

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

在模型上定义 `$hidden` 字段与 casts — 在模型中配置，在测试中断言。

---

## Unit — JSON 字符串

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

## 运行测试

```bash
php pinoox test com_my_shop -f Serialization
```

---

## 相关文档

- [HTTP 测试](./http-tests.md)
- [响应](../basic/responses.md)
- [Eloquent — 序列化](../eloquent-orm/serialization.md)
- [API 资源](../eloquent-orm/api-resources.md)
- [修改器 / 类型转换](../eloquent-orm/mutators-casts.md)

---

[← 返回索引](../README.md)
