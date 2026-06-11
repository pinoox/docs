# Validação

[← Voltar ao índice](../README.md)

A validação do Pinoox 3.x usa **Illuminate Validation** — as mesmas regras familiares (`required`, `email`, …). Aplique-as no seu app via Portal ou Request. Três abordagens padrão:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — no Request injetado
3. **`$this->validate()`** — no controller

> Não existe helper global **`request()`**.

---

## Validação em um controller

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

## Validação com Request

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

Se os dados forem inválidos, uma **`ValidationException`** é lançada.

---

## Validação com tratamento manual de erros (formulários)

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

## Regras comuns

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

## Regra personalizada

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## Mensagens traduzidas

Coloque mensagens padrão em `lang/{locale}/validation.lang.php` do seu app:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## Exemplo de API com catch

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

## Dicas

- **Sempre** valide a entrada do usuário antes de acessar Model/DB
- Para campos de arquivo use regras `file`, `image` e `mimes`
- `$request->validation()` fornece um Validator sem lançamento imediato

---

## Documentação relacionada

- [Request](./requests.md)
- [Resposta HTTP](./responses.md)
- [Idioma e tradução](./language.md)
- [Portal](./portal.md)

---

[← Voltar ao índice](../README.md)
