# Валидация

[← Вернуться к оглавлению](../README.md)

Валидация Pinoox 3.x использует **Illuminate Validation** — те же знакомые строки правил (`required`, `email`, …). Применяйте их в приложении через Portal или Request. Три стандартных подхода:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — на внедрённом Request
3. **`$this->validate()`** — в контроллере

> Глобального хелпера **`request()`** нет.

---

## Валидация в контроллере

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

## Валидация через Request

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

При невалидных данных выбрасывается **`ValidationException`**.

---

## Валидация с ручной обработкой ошибок (формы)

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

## Частые правила

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

## Пользовательское правило

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## Переведённые сообщения

Размещайте сообщения по умолчанию в `lang/{locale}/validation.lang.php` приложения:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## Пример API с catch

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

## Советы

- **Всегда** валидируйте пользовательский ввод перед доступом к Model/DB
- Для полей файлов используйте правила `file`, `image` и `mimes`
- `$request->validation()` даёт Validator без немедленного throw

---

## Связанные документы

- [Request](./requests.md)
- [HTTP Response](./responses.md)
- [Язык и перевод](./language.md)
- [Portal](./portal.md)

---

[← Вернуться к оглавлению](../README.md)
