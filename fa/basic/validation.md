# اعتبارسنجی (Validation)

[← بازگشت به فهرست](../../readme-fa.md)

اعتبارسنجی در پینوکس ۳.x از **موتور Illuminate Validation** (همان قوانین رایج مثل `required` و `email`) استفاده می‌کند. در اپ خودتان با Portal یا Request اعمالش کنید — سه روش استاندارد:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — روی Request تزریق‌شده
3. **`$this->validate()`** — در کنترلر

> helper سراسری **`request()`** وجود ندارد.

---

## اعتبارسنجی در کنترلر

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

## اعتبارسنجی با Request

```php
$data = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8|confirmed',
]);
```

---

## Portal — Validation::validate()

```php
use Pinoox\Portal\Validation;

$validated = Validation::validate($request->all(), [
    'title' => 'required|string|max:255',
    'email' => 'required|email|unique:users,email',
]);
```

اگر داده نامعتبر باشد، **`ValidationException`** پرتاب می‌شود.

---

## اعتبارسنجی با مدیریت خطا (فرم)

```php
$validator = Validation::make($request->all(), [
    'username' => 'required|min:3',
    'password' => 'required|min:8|confirmed',
], [
    'username.required' => 'نام کاربری الزامی است.',
    'password.min' => 'رمز عبور حداقل ۸ کاراکتر باشد.',
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

## قوانین رایج

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

## قانون سفارشی

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'کد ملی معتبر نیست.');
```

---

## ترجمه پیام‌ها

پیام‌های پیش‌فرض را در `lang/fa/validation.lang.php` اپ خود قرار دهید:

```php
// apps/com_acme_shop/lang/fa/validation.lang.php
return [
    'required' => 'فیلد :attribute الزامی است.',
    'email' => 'فرمت :attribute معتبر نیست.',
];
```

---

## مثال API با catch

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

## نکات

- ورودی کاربر را **همیشه** قبل از Model/DB اعتبارسنجی کنید
- برای فیلدهای فایل از قوانین `file`, `image`, `mimes` استفاده کنید
- `$request->validation()` برای دسترسی به Validator بدون throw فوری

---

## مستندات مرتبط

- [درخواست — Request](./requests.md)
- [پاسخ — Response](./responses.md)
- [زبان و ترجمه](./language.md)
- [Portal — پورتال](./portal.md)

---

[← بازگشت به فهرست](../../readme-fa.md)
