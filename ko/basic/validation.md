# Validation

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x validation은 **Illuminate Validation**을 사용합니다 — 익숙한 rule string (`required`, `email`, …)과 동일합니다. Portal 또는 Request를 통해 앱에 적용하세요. 세 가지 표준 방법:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — inject된 Request
3. **`$this->validate()`** — Controller

> 전역 **`request()`** helper는 없습니다.

---

## Controller에서 Validation

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

## Request로 Validation

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

데이터가 유효하지 않으면 **`ValidationException`**이 던져집니다.

---

## 수동 오류 처리 Validation (form)

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

## 자주 쓰는 rule

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

## Custom rule

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## 번역된 message

기본 message는 앱의 `lang/{locale}/validation.lang.php`에 두세요:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## catch를 사용한 API 예제

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

## Tips

- Model/DB 접근 **전에** 항상 사용자 input을 validate하세요
- file field에는 `file`, `image`, `mimes` rule을 사용하세요
- `$request->validation()`은 즉시 throw 없이 Validator를 제공합니다

---

## 관련 문서

- [Request](./requests.md)
- [HTTP Response](./responses.md)
- [Language and Translation](./language.md)
- [Portal](./portal.md)

---

[← 색인으로 돌아가기](../README.md)
