# پارامترهای Route

[← بازگشت به فهرست](../README.md)

پینوکس از قبل `{id}` را پشتیبانی می‌کند. این صفحه **سینتکس غنی‌تر پارامترها** را توضیح می‌دهد: اختیاری، catch-all، تایپ‌های داخلی، enum، پسوند فایل، و patternهای قابل استفاده مجدد — بدون بازنویسی موتور روتینگ.

> مسیرها **یک‌بار** هنگام ثبت به requirements و defaults سازگار با Symfony کامپایل می‌شوند. مقدار نامعتبر تایپ/enum **match نمی‌شود** → ۴۰۴، بدون اجرای کنترلر.

---

## مثال‌های سریع

```php
use Pinoox\Portal\Route;
use function Pinoox\Router\{get, pattern};

get('/users/{id?}', [UserController::class, 'show']);
get('/docs/{path*}', [DocsController::class, 'page']);
get('/items/{id:int}', [ItemController::class, 'show']);
get('/orders/{status:pending|paid|cancelled}', [OrderController::class, 'byStatus']);
get('/files/{name}.{ext}', [FileController::class, 'show']);
get('/download/{app}/{path*}', [DownloadController::class, 'file']);

pattern('username', '[a-z][a-z0-9_]{2,20}');
get('/u/{username:username}', [ProfileController::class, 'show']);
```

خواندن مقادیر:

```php
$request->route('id');
$request->route('path');
$request->route('status', 'pending');
$request->route(); // آبجکت Route جاری
```

---

## پارامتر اختیاری

```text
/users/{id?}
```

| URL | `id` |
|-----|------|
| `/users` | `null` |
| `/users/15` | `"15"` |

فرم اختیاری + تایپ:

```php
get('/users/{id?:int}', ...);
```

---

## Catch-all

```text
/docs/{path*}
```

| URL | `path` |
|-----|--------|
| `/docs` | `""` |
| `/docs/install` | `"install"` |
| `/docs/install/php/linux` | `"install/php/linux"` |

مقدار، باقی‌ماندهٔ مسیر **بدون** اسلش ابتدایی است.

---

## Catch-all اختیاری

```text
/docs/{path*?}
```

| URL | `path` |
|-----|--------|
| `/docs` | `null` |
| `/docs/install/php` | `"install/php"` |

---

## تایپ‌های داخلی

`{name:type}` — برای موارد رایج نیازی به regex نیست.

| Type | خلاصه |
|------|--------|
| `int` | فقط رقم |
| `number` | عدد صحیح یا اعشاری |
| `uuid` | UUID |
| `ulid` | ULID بیست‌وشش‌کاراکتری |
| `slug` | مثل `my-post-title` |
| `alpha` | فقط حروف |
| `alnum` | حروف و رقم |
| `hex` | هگز |
| `email` | شکل ساده ایمیل |
| `domain` | دامنه |
| `ip` | IPv4 / IPv6 |
| `url` | آدرس `http`/`https` |

مقدار نامعتبر → **۴۰۴**.

---

## Enum

```php
get('/orders/{status:pending|paid|cancelled}', ...);
```

`/orders/refunded` → ۴۰۴.

---

## پسوند فایل

```php
get('/files/{name}.{ext}', ...);
// /files/logo.png → name=logo, ext=png
```

---

## چند پارامتر

```php
get('/download/{app}/{path*}', ...);
// app=pinoox ، path=releases/v3/file.zip
```

---

## Pattern سفارشی

```php
Route::pattern('username', '[a-z][a-z0-9_]{2,20}');
Route::patterns(['snowflake' => '[0-9]{19}']);

get('/users/{username:username}', ...);
```

| API | نقش |
|-----|-----|
| `pattern` / `Route::pattern` | یک الگو |
| `patterns` / `Route::patterns` | چند الگو |
| `Route::hasPattern` | وجود دارد؟ (شامل built-in) |
| `Route::clearPatterns` | پاک‌کردن سفارشی‌ها و بازگردانی built-in |

constraint ناشناخته به‌عنوان **regex خام** برای همان پارامتر استفاده می‌شود. `->filters()` صریح روی route، برای همان کلید بر تایپ کامپایل‌شده اولویت دارد.

---

## اولویت match

1. مسیرهای **exact**  
2. پارامترهای **typed / enum / required**  
3. پارامترهای **optional**  
4. **Catch-all**  
5. **Fallback** (`*`)

جزئیات fallback: [Fallback Routes](./fallback-routes.md).

---

## کارایی

- کامپایل در **ثبت route**، نه در هر درخواست  
- رجیستری استاتیک patternها  
- بدون بازسازی مکرر regex در runtime برای تایپ‌ها  

---

## سازگاری عقب‌رو

- `{id}` ساده مثل قبل  
- `filters` / `defaults` موجود  
- fallback بدون تغییر  
- API عمومی فقط **افزوده** شده است  
