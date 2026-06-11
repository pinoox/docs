# Validation

[← 索引に戻る](../README.md)

Pinoox 3.x の Validation は **Illuminate Validation** を使用します — おなじみのルール文字列（`required`、`email` など）。Portal または Request 経由でアプリに適用します。標準的な 3 つのアプローチ:

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — 注入された Request 上
3. **`$this->validate()`** — Controller 内

> グローバル **`request()`** ヘルパーはありません。

---

## Controller での Validation

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

## Request による Validation

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

データが無効な場合、**`ValidationException`** がスローされます。

---

## 手動エラーハンドリング付き Validation（フォーム）

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

## よく使うルール

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

## カスタムルール

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## 翻訳されたメッセージ

デフォルトメッセージはアプリの `lang/{locale}/validation.lang.php` に置きます。

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## catch 付き API 例

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

## ヒント

- Model/DB アクセスの前に **必ず** ユーザー入力を Validation する
- ファイルフィールドには `file`、`image`、`mimes` ルールを使用
- `$request->validation()` は即座にスローせず Validator を返す

---

## 関連ドキュメント

- [Request](./requests.md)
- [HTTP Response](./responses.md)
- [言語と翻訳](./language.md)
- [Portal](./portal.md)

---

[← 索引に戻る](../README.md)
