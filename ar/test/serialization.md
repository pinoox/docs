# اختبار التسلسل في Pinoox

[← العودة إلى الفهرس](../README.md)

لـ APIs وResources، افحص مخرجات JSON بـ `assertJsonPath()` و`json()` على `TestResponse`. لنماذج Eloquent، أكّد `toArray()` / `toJson()` داخل `inApp()`.

---

## API — بنية الغلاف

استجابات API في Pinoox عادةً تتضمن حقول `success` و`data`:

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

## قراءة جزء من JSON

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — استجابة المورد المُنشأ

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

## Unit — toArray للنموذج

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

عرّف حقول `$hidden` وcasts على النموذج — اضبط هناك، أكّد في الاختبارات.

---

## Unit — سلسلة JSON

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

## تشغيل الاختبارات

```bash
php pinoox test com_my_shop -f Serialization
```

---

## وثائق ذات صلة

- [اختبارات HTTP](./http-tests.md)
- [الاستجابات (Responses)](../basic/responses.md)
- [Eloquent — التسلسل](../eloquent-orm/serialization.md)
- [موارد API (API resources)](../eloquent-orm/api-resources.md)
- [Mutators / casts](../eloquent-orm/mutators-casts.md)

---

[← العودة إلى الفهرس](../README.md)
