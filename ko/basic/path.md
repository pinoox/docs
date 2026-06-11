# File Path

[← 색인으로 돌아가기](../README.md)

디스크의 file과 folder에 접근할 때 **`path()`**와 **`Pinoox\Portal\Path`** Portal을 사용하세요. 프로젝트 설치 위치와 `apps/` 폴더 이름과 무관하게 code를 유지할 수 있습니다.

---

## 표준 방법 — `path()`

```php
// Path relative to the active app
$logDir = path('storage/logs');
// → …/apps/com_acme_shop/storage/logs

// Config file in another app
$configFile = path('config/payment.php', 'com_acme_shop');

// App root
$appRoot = path('', 'com_acme_shop');
// or
use Pinoox\Portal\Path;
$appRoot = Path::app('com_acme_shop');
```

---

## 일반적인 사용

### File 읽기 / 쓰기

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Translation file 경로

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Theme 경로

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

명시적 API로 `path()`와 동일한 동작:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // current app
Path::app('com_pinoox_manager'); // specific app
```

---

## `path()` vs `url()`

| Helper | Output | Example |
|--------|--------|---------|
| `path()` | Physical path on the server | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | HTTP URL for the browser | `https://site.com/pinoox/shop/products` |

---

## 예제: upload service

`path()` + `move_uploaded_file()`로 upload를 수동 작성하지 마세요 — **`File`** portal을 사용하면 file이 프로젝트 `storage/` 폴더에 저장됩니다:

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // stored under storage/apps/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

전체 upload API는 [File management](../advanced/file-management.md)를 참조하세요.

---

## Tips

- 브라우저에서 접근 가능한 경로는 `path()`가 아니라 `url()` 또는 `assets()`를 사용하세요
- 비활성 앱이 필요할 때만 package 이름을 전달하세요
- path segment는 `/`로 연결; Path가 OS slash를 올바르게 처리합니다

---

## 관련 문서

- [URL 및 링크](./url.md)
- [Config](./config.md)
- [App Services](../advanced/services.md)
- [Helpers](../advanced/helpers.md)

---

[← 색인으로 돌아가기](../README.md)
