# 文件路径（Path）

[← 返回索引](../README.md)

使用 **`path()`** 和 **`Pinoox\Portal\Path`** Portal 访问磁盘上的文件和文件夹。这样代码就不会依赖项目的安装位置以及 `apps/` 文件夹的名称。

---

## 标准方式 — `path()`

```php
// 相对于当前激活应用的路径
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// 其他应用中的配置文件
$configFile = path('config/payment.php', 'com_acme_shop');

// 应用根目录
$appRoot = path('', 'com_acme_shop');
// 或者
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## 常见用法

### 读写文件

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### 翻译文件路径

```php
$langFile = path('lang/en/welcome.lang.php');
```

### 主题路径

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

与 `path()` 行为相同，但 API 更明确：

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // 当前应用
Path::app('com_pinoox_manager'); // 指定应用
```

---

## `path()` 与 `url()` 的区别

| 辅助函数 | 输出 | 示例 |
|--------|--------|---------|
| `path()` | 服务器上的物理路径 | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | 浏览器使用的 HTTP URL | `https://site.com/pinoox/shop/products` |

---

## 示例：上传服务

不要用 `path()` + `move_uploaded_file()` 手动写入上传文件 — 请使用 **`File`** Portal，让文件落入项目的 `storage/` 文件夹：

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // 存储在 storage/local/com_acme_shop/{subdir} 下
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

完整的上传 API 请见[文件管理](../advanced/file-management.md)。

---

## 小贴士

- 浏览器可访问的路径请使用 `url()` 或 `assets()`，而不是 `path()`。
- 仅在需要访问非当前激活应用时才传入包名。
- 路径片段用 `/` 连接；Path 会处理操作系统对应的正确斜杠。

---

## 相关文档

- [URL 与链接](./url.md)
- [配置（Config）](./config.md)
- [应用服务（Services）](../advanced/services.md)
- [辅助函数（Helpers）](../advanced/helpers.md)

---

[← 返回索引](../README.md)
