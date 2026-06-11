# 验证（Validation）

[← 返回索引](../README.md)

Pinoox 3.x 的验证基于 **Illuminate Validation** — 使用同样熟悉的规则字符串（`required`、`email` 等）。在应用中可以通过 Portal 或 Request 来使用。三种标准方式：

1. **`Validation::validate()`** — Portal
2. **`$request->validate()`** — 在注入的 Request 上
3. **`$this->validate()`** — 在控制器中

> 不存在全局的 **`request()`** 辅助函数。

---

## 在控制器中验证

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

## 使用 Request 验证

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

如果数据无效，会抛出 **`ValidationException`**。

---

## 手动处理错误的验证（表单）

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

## 常用规则

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

## 自定义规则

```php
Validation::extend('national_code', function ($attribute, $value) {
    return preg_match('/^\d{10}$/', $value);
}, 'Invalid national ID.');
```

---

## 翻译后的消息

将默认消息放在应用的 `lang/{locale}/validation.lang.php` 中：

```php
// apps/com_acme_shop/lang/en/validation.lang.php
return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute format is invalid.',
];
```

---

## 带异常捕获的 API 示例

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

## 小贴士

- 在访问 Model/数据库之前，**务必**验证用户输入
- 文件字段使用 `file`、`image` 和 `mimes` 规则
- `$request->validation()` 返回一个 Validator 而不会立即抛出异常

---

## 相关文档

- [请求（Request）](./requests.md)
- [HTTP 响应（Response）](./responses.md)
- [语言与翻译](./language.md)
- [Portal](./portal.md)

---

[← 返回索引](../README.md)
