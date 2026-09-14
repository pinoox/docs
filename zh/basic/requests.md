# HTTP 请求（Request）

[← 返回索引](../README.md)

`Pinoox\Component\Http\Request` 类负责处理 HTTP 输入：查询字符串、表单 POST、JSON 请求体、路由参数和文件上传。在控制器和 Flow 中，`Request` 通过方法参数的**依赖注入**获得。

> 不存在全局的 **`request()`** 辅助函数。请注入 `Request`，或在控制器中使用 `$this->getRequest()`。

---

## 在控制器中访问

```php
use Pinoox\Component\Http\Request;

public function index(Request $request)
{
    $search = $request->get('search');
    // ...
}
```

`$request->get($key)` 返回 attributes、POST、query、JSON 和文件合并后的数据。

---

## 从特定来源读取

| 来源 | 方法 | 示例 |
|--------|--------|---------|
| 查询字符串 | `queryOne()` | `$request->queryOne('page', 1)` |
| 表单 POST | `requestOne()` | `$request->requestOne('email')` |
| JSON 请求体 | `jsonOne()` | `$request->jsonOne('items')` |
| 路由参数 | `parametersOne()` | `$request->parametersOne('id')` |
| 全部输入 | `all()` | `$request->all()` |

```php
// ?search=foo
$search = $request->queryOne('search');

// POST 字段：email
$email = $request->requestOne('email');

// 路由：/product/{id}
$id = $request->parametersOne('id');
```

---

## 验证

```php
$validated = $request->validate([
    'email' => 'required|email',
    'password' => 'required|min:8',
]);
```

或者获取一个 Validator 实例：

```php
$validator = $request->validation([
    'title' => 'required|max:255',
]);

if ($validator->fails()) {
    return response()->json(['errors' => $validator->errors()], 422);
}
```

基础控制器也提供 **`$this->validate()`** 和 **`$this->validation()`**。

---

## 文件上传

```php
$file = $request->file('avatar');

$uploader = $request->store('avatar', 'avatars'); // → storage/local/{package}/avatars
if ($uploader) {
    $path = $uploader->getPath();
}
```

---

## 检测请求类型

```php
if ($request->isXmlHttpRequest()) {
    // Ajax
}

if ($request->isJson()) {
    // Content-Type: application/json
}
```

---

## 当前路由与集合

```php
$route = $request->route();
$collection = $request->collection();
```

---

## 完整的 API 控制器示例

```php
<?php

namespace App\com_acme_shop\Controller;

use App\com_acme_shop\Model\ProductModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class ProductApiController extends ApiController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|max:200',
            'price' => 'required|numeric|min:0',
        ]);

        $product = ProductModel::create($data);

        return $this->ok($product, status: 201);
    }
}
```

---

## 指导原则

- 始终验证用户输入
- 对于 API，使用 `jsonOne()` 或 `get()` 读取 JSON
- `Request` 也可以注入到 Flow 中

---

## 相关文档

- [控制器（Controllers）](./controllers.md)
- [HTTP 响应（Response）](./responses.md)
- [验证（Validation）](./validation.md)
- [路由（Router）](./routers.md)

---

[← 返回索引](../README.md)
