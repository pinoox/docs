# Validación

[← Volver al índice](../README.md)

La validación de Pinoox 3.x usa **Illuminate Validation** — las mismas cadenas de reglas conocidas (`required`, `email`, …). Aplícalas en tu app mediante el Portal o el Request. Tres enfoques estándar:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — en el Request inyectado
3. **`$this->validate()`** — en el controller

> No hay un helper global **`request()`**.

---

## Validación en un controller

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

## Validación con el Request

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

Si los datos no son válidos, se lanza una **`ValidationException`**.

---

## Validación con manejo manual de errores (formularios)

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

## Reglas comunes

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

## Regla personalizada

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## Mensajes traducidos

Coloca los mensajes predeterminados en `lang/{locale}/validation.lang.php` de tu app:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## Ejemplo de API con catch

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

## Consejos

- Valida **siempre** la entrada del usuario antes de acceder al Model/BD
- Para campos de archivo usa las reglas `file`, `image` y `mimes`
- `$request->validation()` te da un Validator sin lanzar una excepción de inmediato

---

## Documentación relacionada

- [Request](./requests.md)
- [Response HTTP](./responses.md)
- [Idioma y traducción](./language.md)
- [Portal](./portal.md)

---

[← Volver al índice](../README.md)
