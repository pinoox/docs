# Validation

[← इंडेक्स पर वापस जाएँ](../README.md)

Pinoox 3.x validation **Illuminate Validation** उपयोग करता है — वही परिचित rule strings (`required`, `email`, …)। App में Portal या Request के ज़रिए apply करें। तीन standard approaches:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — injected Request पर
3. **`$this->validate()`** — controller में

> Global **`request()`** helper नहीं है।

---

## Controller में validation

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

## Request के साथ validation

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

Data invalid होने पर **`ValidationException`** throw होता है।

---

## Manual error handling के साथ validation (forms)

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

## Common rules

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

## Translated messages

Default messages app की `lang/{locale}/validation.lang.php` में रखें:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## catch के साथ API example

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

- Model/DB access से **पहले** user input validate करें
- File fields के लिए `file`, `image`, और `mimes` rules उपयोग करें
- `$request->validation()` immediate throw के बिना Validator देता है

---

## संबंधित docs

- [Request](./requests.md)
- [HTTP Response](./responses.md)
- [Language and Translation](./language.md)
- [Portal](./portal.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
