# Validierung

[← Zurück zur Übersicht](../README.md)

Die Validierung in Pinoox 3.x nutzt **Illuminate Validation** — dieselben vertrauten Regel-Strings (`required`, `email`, …). Wenden Sie sie in Ihrer App über das Portal oder den Request an. Drei Standardansätze:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — auf dem injizierten Request
3. **`$this->validate()`** — im Controller

> Es gibt keinen globalen **`request()`**-Helfer.

---

## Validierung in einem Controller

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

## Validierung mit Request

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

Bei ungültigen Daten wird eine **`ValidationException`** geworfen.

---

## Validierung mit manueller Fehlerbehandlung (Formulare)

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

## Häufige Regeln

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

## Eigene Regel

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## Übersetzte Meldungen

Legen Sie Standardmeldungen in der Datei `lang/{locale}/validation.lang.php` Ihrer App ab:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## API-Beispiel mit catch

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

## Tipps

- Validieren Sie Benutzereingaben **immer** vor dem Model-/DB-Zugriff
- Verwenden Sie für Dateifelder die Regeln `file`, `image` und `mimes`
- `$request->validation()` liefert einen Validator ohne sofortiges Werfen einer Exception

---

## Verwandte Dokumente

- [Request](./requests.md)
- [HTTP-Response](./responses.md)
- [Sprache und Übersetzung](./language.md)
- [Portal](./portal.md)

---

[← Zurück zur Übersicht](../README.md)
