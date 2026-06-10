# تست سریال‌سازی در پینوکس

برای API و Resourceها، خروجی JSON را با `assertJsonPath()` و `json()` روی `TestResponse` بررسی کنید. برای مدل Eloquent، `toArray()` / `toJson()` را داخل `inApp()` assert کنید.

---

## API — ساختار envelope

پاسخ‌های API پینوکس معمولاً فیلدهای `success` و `data` دارند:

```php
// apps/com_my_shop/tests/Feature/ProductSerializationTest.php

it('serializes product in api envelope', function () {
    inApp(appPackage(), function () {
        App\com_my_shop\Model\ProductModel::create([
            'title' => 'ماوس',
            'price' => 250000,
        ]);
    });

    $response = appGet(appPackage(), '/api/v1/products/1');

    $response
        ->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('data.title', 'ماوس')
        ->assertJsonPath('data.price', 250000);
});
```

---

## خواندن بخشی از JSON

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — پاسخ ایجاد رکورد

```php
it('returns created resource', function () {
    $response = appPostJson(appPackage(), '/api/v1/products', [
        'title' => 'کیبورد',
        'price' => 1800000,
    ]);

    $response
        ->assertStatus(201)
        ->assertJsonPath('data.title', 'کیبورد')
        ->and($response->json('data.id'))->toBeInt();
});
```

---

## Unit — toArray مدل

```php
// apps/com_my_shop/tests/Unit/ProductArrayTest.php

it('hides internal fields in array', function () {
    inApp(appPackage(), function () {
        $product = App\com_my_shop\Model\ProductModel::make([
            'title' => 'تست',
            'internal_note' => 'محرمانه',
        ]);

        $array = $product->toArray();

        expect($array)->toHaveKey('title')
            ->and($array)->not->toHaveKey('internal_note');
    });
});
```

فیلدهای `$hidden` و castها در مدل تعریف می‌شوند — همان‌جا تنظیم کنید، در تست فقط assert کنید.

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

## اجرا

```bash
php pinoox test com_my_shop -f Serialization
```

---

## مستندات مرتبط

- [تست HTTP](./http-tests.md)
- [Responses](../basic/responses.md)
- [Eloquent — سریال‌سازی](../eloquent-orm/serialization.md)
- [منابع API](../eloquent-orm/api-resources.md)
- [Mutatorها / Castها](../eloquent-orm/mutators-casts.md)
