# Pinoox'ta serileştirme testleri

[← Dizine dön](../README.md)

API ve Resource'lar için JSON çıktısını `TestResponse` üzerinde `assertJsonPath()` ve `json()` ile inceleyin. Eloquent model'leri için `inApp()` içinde `toArray()` / `toJson()` doğrulayın.

---

## API — zarf yapısı

Pinoox API yanıtları genellikle `success` ve `data` alanlarını içerir:

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

## JSON'un bir bölümünü okuma

```php
it('returns items array', function () {
    $response = appGet(appPackage(), '/api/v1/products');

    $items = $response->json('data.items');

    expect($items)->toBeArray()
        ->and(count($items))->toBeGreaterThan(0);
});
```

---

## POST — oluşturulan kaynak yanıtı

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

`$hidden` alanları ve cast'leri model'de tanımlayın — orada yapılandırın, testlerde doğrulayın.

---

## Unit — JSON dizesi

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

## Testleri çalıştırma

```bash
php pinoox test com_my_shop -f Serialization
```

---

## İlgili dokümantasyon

- [HTTP testleri](./http-tests.md)
- [Response](../basic/responses.md)
- [Eloquent — serileştirme](../eloquent-orm/serialization.md)
- [API Resource'lar](../eloquent-orm/api-resources.md)
- [Mutator'lar / cast'ler](../eloquent-orm/mutators-casts.md)

---

[← Dizine dön](../README.md)
