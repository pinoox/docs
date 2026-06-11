# Validation

[← Retour à l'index](../README.md)

La validation Pinoox 3.x utilise **Illuminate Validation** — les mêmes chaînes de règles familières (`required`, `email`, …). Appliquez-les dans votre app via Portal ou Request. Trois approches standard :

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — sur Request injecté
3. **`$this->validate()`** — dans le contrôleur

> Il n'existe pas de helper global **`request()`**.

---

## Validation dans un contrôleur

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

## Validation avec Request

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

Si les données sont invalides, une **`ValidationException`** est levée.

---

## Validation avec gestion manuelle des erreurs (formulaires)

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

## Règles courantes

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

## Règle personnalisée

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## Messages traduits

Placez les messages par défaut dans `lang/{locale}/validation.lang.php` de votre app :

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## Exemple API avec catch

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

## Conseils

- **Validez toujours** les entrées utilisateur avant l'accès Model/DB
- Pour les champs fichier, utilisez les règles `file`, `image` et `mimes`
- `$request->validation()` vous donne un Validator sans levée immédiate

---

## Documentation associée

- [Request](./requests.md)
- [Réponse HTTP](./responses.md)
- [Langue et traduction](./language.md)
- [Portal](./portal.md)

---

[← Retour à l'index](../README.md)
