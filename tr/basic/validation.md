# Validasyon

[← Dizine dön](../README.md)

Pinoox 3.x validasyonu **Illuminate Validation** kullanır — aynı tanıdık kural dizeleri (`required`, `email`, …). Uygulamanızda Portal veya Request üzerinden uygulayın. Üç standart yaklaşım:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — enjekte edilmiş Request üzerinde
3. **`$this->validate()`** — controller'da

> Global **`request()`** helper'ı yoktur.

---

## Controller'da validasyon

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

## Request ile validasyon

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

Veri geçersizse **`ValidationException`** fırlatılır.

---

## Manuel hata işleme ile validasyon (formlar)

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

## Yaygın kurallar

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

## Özel kural

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## Çevrilmiş mesajlar

Varsayılan mesajları uygulamanızın `lang/{locale}/validation.lang.php` dosyasına koyun:

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## catch ile API örneği

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

## İpuçları

- Model/DB erişiminden **önce** kullanıcı girdisini her zaman doğrulayın
- Dosya alanları için `file`, `image` ve `mimes` kurallarını kullanın
- `$request->validation()` anında fırlatma olmadan Validator verir

---

## İlgili dokümantasyon

- [Request](./requests.md)
- [HTTP Response](./responses.md)
- [Dil ve çeviri](./language.md)
- [Portal](./portal.md)

---

[← Dizine dön](../README.md)
