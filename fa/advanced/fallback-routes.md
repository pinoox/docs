# Fallback Routes

[← بازگشت به فهرست](../README.md)

وقتی هیچ route معمولی match نشود، پینوکس می‌تواند یک **fallback** برای همان محدودهٔ URL اجرا کند. اولویت با نزدیک‌ترین prefix است.

> از **`Route::fallback()`** یا helper **`fallback()`** استفاده کنید. بدون fallback، پاسخ پیش‌فرض ۴۰۴ فریمورک باقی می‌ماند.

---

## Fallback سراسری

```php
use function Pinoox\Router\fallback;

fallback(fn () => view('404'))->name('fallback');
```

---

## Fallback گروه / API

```php
group(['prefix' => '/api', 'flows' => ['cors:api']], function () {
    get('/products', [ProductController::class, 'index']);

    fallback(fn () => response()->json(['message' => 'Not Found'], 404))
        ->name('fallback.api');
});
```

---

## prefix

```php
Route::prefix('/admin', function () {
    Route::fallback(Admin404Controller::class)->name('fallback.admin');
});
```

---

## Flow

```php
Route::fallback(fn () => response()->json(['message' => 'Not Found'], 404))
    ->flow(['cors:api']);
```

---

## اولویت

`/api/missing` → fallback گروه API  
`/other` → fallback سراسری  

---

## مستندات مرتبط

- [روتر](../basic/routers.md)
- [فلو](../basic/flows.md)

---

[← بازگشت به فهرست](../README.md)
