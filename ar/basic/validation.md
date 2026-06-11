# التحقق (Validation)

[← العودة إلى الفهرس](../README.md)

يستخدم Pinoox 3.x **Illuminate Validation** — نفس سلاسل القواعد المألوفة (`required`، `email`، …). طبّقها في تطبيقك عبر Portal أو Request. ثلاثة أساليب معيارية:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — على Request المحقون
3. **`$this->validate()`** — في المتحكم

> لا يوجد مساعد عام **`request()`**.

---

## التحقق في متحكم

```php
use Pinoox\Component\Http\Request;

public function store(Request $request)
{
    $validated = $this->validate([
        'title' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
    ]);

    ProductModel::create($validated);
    return redirect(url('products'));
}
```

---

## التحقق مع Request

```php
$data = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8|confirmed',
]);
```

---

## Portal — `Validation::validate()`

```php
use Pinoox\Portal\Validation;

$validated = Validation::validate($request->all(), [
    'title' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
]);
```

إذا كانت البيانات غير صالحة، يُرمى **`ValidationException`**.

---

## التحقق مع معالجة الأخطاء يدويًا (النماذج)

```php
$validator = Validation::make($request->all(), [
    'username' => 'required|min:3',
    'password' => 'required|min:8|confirmed',
], [
    'username.required' => 'Username is required.',
    'password.min' => 'Password must be at least 8 characters.',
]);

if ($validator->fails()) {
    return View::render('auth/register', [
        'errors' => $validator->errors()->all(),
        'old' => $request->all(),
    ]);
}

UserModel::create($validator->validated());
return redirect(url('login'));
```

---

## ApiController

```php
$data = $this->validate([
    'title' => 'required|max:200',
]);

return $this->ok(ProductModel::create($data), status: 201);
```

---

## قواعد شائعة

```php
$rules = [
    'name'     => 'required|string|max:100',
    'mobile'   => 'required|regex:/^09[0-9]{9}$/',
    'role_id'  => 'required|exists:roles,id',
    'avatar'   => 'nullable|image|max:2048',
    'tags'     => 'array',
    'tags.*'   => 'string|max:50',
];
```

---

## قاعدة مخصصة

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## رسائل مترجمة

ضع الرسائل الافتراضية في `lang/{locale}/validation.lang.php` لتطبيقك:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## مثال API مع catch

```php
use Pinoox\Component\Http\Request;
use Pinoox\Component\Validation\ValidationException;
use Pinoox\Portal\Validation;

public function save(Request $request)
{
    try {
        $data = Validation::validate($request->all(), [
            'title' => 'required|string|max:200',
            'stock' => 'required|integer|min:0',
        ]);

        ProductModel::updateOrCreate(['id' => $request->get('id')], $data);

        return response()->json(['success' => true]);
    } catch (ValidationException $e) {
        return response()->json(['errors' => $e->errors()], 422);
    }
}
```

---

## نصائح

- **تحقق دائمًا** من مدخلات المستخدم قبل الوصول إلى Model/DB
- لحقول الملفات استخدم قواعد `file` و`image` و`mimes`
- `$request->validation()` يعطيك Validator دون رمي فوري

---

## وثائق ذات صلة

- [الطلب (Request)](./requests.md)
- [استجابة HTTP (Response)](./responses.md)
- [اللغة والترجمة](./language.md)
- [Portal](./portal.md)

---

[← العودة إلى الفهرس](../README.md)
