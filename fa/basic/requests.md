# Request (درخواست HTTP)

[← بازگشت به فهرست](../README.md)

کلاس `Pinoox\Component\Http\Request` ورودی HTTP را مدیریت می‌کند: query string، فرم POST، JSON body، پارامتر route و فایل آپلود. در کنترلر و Flow، Request با **تزریق وابستگی** به متد در دسترس است.

> helper سراسری **`request()`** وجود ندارد. Request را inject کنید یا از `$this->getRequest()` در کنترلر استفاده کنید.

---

## دسترسی در کنترلر

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` داده‌های ترکیبی (attributes + POST + query + JSON + files) را برمی‌گرداند.

---

## خواندن داده از منبع مشخص

| منبع | متد | مثال |
|------|-----|------|
| Query string | `queryOne()` | `$request->queryOne('page', 1)` |
| فرم POST | `requestOne()` | `$request->requestOne('email')` |
| JSON body | `jsonOne()` | `$request->jsonOne('items')` |
| پارامتر route | `parametersOne()` | `$request->parametersOne('id')` |
| همه | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST field: email
$email = $request->requestOne('email');

// Route: /product/{id}
$id = $request->parametersOne('id');
```

---

## اعتبارسنجی

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

یا دریافت Validator:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

در کنترلر پایه هم **`$this->validate()`** و **`$this->validation()`** در دسترس است.

---

## فایل آپلود

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## تشخیص نوع درخواست

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## بدنه QUERY

برای مسیرهایی که با `query()` (متد HTTP `QUERY`) ثبت شده‌اند، بدنه را مثل POST/JSON بخوانید:

```php
public function search(Request $request)
{
    $filters = $request->getPayload()->all();
    // یا: $request->jsonOne('filters')
}
```

بیشتر: [روتر — متد QUERY](./routers.md#متد-query-rfc-10008).

---

## Route و Collection فعلی

```php
$route = $request->route();
$collection = $request->collection();
```

---

## مثال کامل در کنترلر API

```php
<?php

namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Model\ProductModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:200',
            'price' => 'required|numeric|min:0',
        ]);

        $product = ProductModel::create($data);

        return $this->ok($product, status: 201);
    }
}
```

---

## نکات

- همیشه ورودی کاربر را validate کنید
- برای API، JSON را با `jsonOne()` یا `get()` بخوانید
- Request را به Flow هم می‌توان تزریق کرد

---

## مستندات مرتبط

- [کنترلر](./controllers.md)
- [پاسخ — Response](./responses.md)
- [اعتبارسنجی — Validation](./validation.md)
- [روتر](./routers.md)

---

[← بازگشت به فهرست](../README.md)
