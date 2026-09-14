# طلب HTTP (Request)

[← العودة إلى الفهرس](../README.md)

تتولى فئة `Pinoox\Component\Http\Request` مدخلات HTTP: سلسلة الاستعلام، POST من النموذج، جسم JSON، معاملات المسار، ورفع الملفات. في المتحكمات وFlows، يتوفر `Request` عبر **حقن التبعية** في معاملات الدالة.

> لا يوجد مساعد عام **`request()`**. حقن `Request` أو استخدم `$this->getRequest()` في المتحكمات.

---

## الوصول في متحكم

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` يُرجع بيانات مدمجة من attributes وPOST وquery وJSON والملفات.

---

## القراءة من مصدر محدد

| المصدر | الدالة | مثال |
|--------|--------|---------|
| سلسلة الاستعلام | `queryOne()` | `$request->queryOne('page', 1)` |
| POST من النموذج | `requestOne()` | `$request->requestOne('email')` |
| جسم JSON | `jsonOne()` | `$request->jsonOne('items')` |
| معامل المسار | `parametersOne()` | `$request->parametersOne('id')` |
| كل المدخلات | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST field: email
$email = $request->requestOne('email');

// Route: /product/{id}
$id = $request->parametersOne('id');
```

---

## التحقق

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

أو احصل على مثيل Validator:

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

يوفر المتحكم الأساسي أيضًا **`$this->validate()`** و**`$this->validation()`**.

---

## رفع ملف

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## اكتشاف نوع الطلب

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## المسار والمجموعة الحالية

```php
$route = $request->route();
$collection = $request->collection();
```

---

## مثال متحكم API كامل

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

## إرشادات

- تحقق دائمًا من مدخلات المستخدم
- لـ APIs، اقرأ JSON بـ `jsonOne()` أو `get()`
- يمكن حقن `Request` في Flows أيضًا

---

## وثائق ذات صلة

- [المتحكمات (Controllers)](./controllers.md)
- [استجابة HTTP (Response)](./responses.md)
- [التحقق (Validation)](./validation.md)
- [المُوجّه (Router)](./routers.md)

---

[← العودة إلى الفهرس](../README.md)
