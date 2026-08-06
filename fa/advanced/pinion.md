# پروتکل Pinion

[← بازگشت به فهرست](../README.md)

**Pinion** پروتکل رسمی آپلود تکه‌ای و قابل‌ادامه پینوکس است. موتور در [`pinoox/pinion`](https://packagist.org/packages/pinoox/pinion) (PHP) و [`@pinooxhq/pinion-client`](https://www.npmjs.com/package/@pinooxhq/pinion-client) (مرورگر) قرار دارد. پینوکس پل ارتباطی اضافه می‌کند: Portal، یکپارچه‌سازی Flysystem/S3، آداپتور HTTP، CLI و traitهای HMVC.

شناسه پروتکل: `pinion` · نسخه: `2`

> استفاده خارج از پینوکس: [README پکیج](../../pinion/README.md)

---

## شروع سریع

**مرورگر** (بدون Axios):

```javascript
import { uploadFile } from '@pinooxhq/pinion-client';

await uploadFile(file, {
  baseURL: '/api/v1/upload',
  unwrapPreset: 'pinoox',
  onProgress: ({ percent }) => console.log(percent + '%'),
});
```

**سرور** (در هر اپ):

```php
use Pinoox\Component\Pinion\Concerns\PinionUploadActions;
use Pinoox\Component\Kernel\Controller\ApiController;

class PinionController extends ApiController
{
    use PinionUploadActions;

    protected function pinionDefaults(): array
    {
        return [
            'destination' => 'uploads/media',
            'extensions' => ['mp4', 'zip'],
            'mode' => 'auto',
            'record' => true,
        ];
    }
}
```

سه مرحله HTTP: **init → upload parts → complete**

---

## چرا Pinion؟

| مشکل | راه‌حل |
|------|--------|
| سقف آپلود هاست | partهای ۵ مگابایتی |
| قطع اتصال | Resume با `fingerprint` |
| خرابی part | `chunk_hash` (SHA-256) |
| فایل بزرگ روی S3 | stage محلی، انتشار به Flysystem در `complete` |

Pinion جایگزین object storage نیست. محدودیت PHP را حل می‌کند و در حالت `auto`/`storage` فایل را به [مدیریت فایل](./file-management.md) می‌سپارد.

---

## معماری در پینوکس

| لایه | مسیر |
|------|------|
| موتور پروتکل | `packages/pinion` |
| پل | `pincore/Component/Pinion/*` |
| Portal | `Pinoox\Portal\Pinion` |
| تنظیمات | `pincore/config/pinion.config.php` |
| chunk موقت | `storage/pinion` |
| قالب route | `pincore/config/pinion.routes.template.php` |

### حالت‌های storage (`defaults.mode`)

| حالت | رفتار |
|------|--------|
| `auto` | اگر دیسک اپ `s3` باشد → `Portal\File` / Flysystem |
| `storage` | همیشه از storage مدیریت‌شده + ردیف `FileModel` |
| `local` | مونتاژ روی `path()` — بدون `pinx_file` |

chunkها در `storage/pinion` نگه داشته می‌شوند. در `complete` مدیریت‌شده، `StorageCompletion` فایل را به دیسک اپ منتقل می‌کند و `file_id`، `url`، `thumb` برمی‌گرداند.

---

## پیکربندی

```env
PINION_CHUNK_SIZE=5242880
PINION_TTL=86400
PINION_PATH=~storage/pinion
PINION_MODE=auto
```

| کلید | توضیح |
|------|--------|
| `chunk_size` | اندازه part |
| `ttl` | عمر session |
| `storage_path` | فضای موقت |
| `defaults.mode` | `auto`، `storage`، `local` |
| `defaults.record` | ساخت ردیف `FileModel` |

---

## کنترلر HMVC

از trait `PinionUploadActions` در `apps/{package}/` استفاده کنید:

```php
use Pinoox\Component\Pinion\Concerns\PinionUploadActions;

class MediaUploadController extends ApiController
{
    use PinionUploadActions;

    protected function pinionDefaults(): array
    {
        return [
            'destination' => 'uploads/media',
            'extensions' => ['mp4', 'mov'],
            'mode' => 'auto',
            'record' => true,
            'disk' => 'public',
            'group' => 'media',
        ];
    }
}
```

**فقط local** (مثلاً پکیج `.pinx` منجر):

```php
protected function pinionDefaults(): array
{
    return [
        'destination' => 'downloads/packages/manual',
        'extensions' => ['pinx'],
        'mode' => 'local',
        'storage' => false,
        'record' => false,
    ];
}
```

`baseURL` کلاینت = پیشوند route (مثلاً `'/app/pinion'`).

---

## Portal (برنامه‌نویسی)

```php
use Pinoox\Portal\Pinion;

$result = Pinion::begin()
    ->filename('backup.zip')
    ->size(524288000)
    ->to('downloads/archives')
    ->fingerprint($clientFingerprint)
    ->init();

Pinion::receive($uploadId, $index, $chunkBinary, $chunkHash);
$complete = Pinion::complete($uploadId);
```

---

## پروتکل HTTP

| مرحله | متد | فیلدها |
|-------|-----|--------|
| Init | `POST {prefix}/init` | `filename`, `size`, `fingerprint`, … |
| Part | `POST {prefix}/upload` | `upload_id`, `index`, `chunk`, `chunk_hash` |
| Complete | `POST {prefix}/complete` | `upload_id` |
| Status | `GET {prefix}/status/{id}` | — |
| Abort | `POST {prefix}/abort/{id}` | — |

### پاسخ complete مدیریت‌شده

```json
{
  "file_id": 42,
  "url": "https://cdn.example.com/…",
  "path": "uploads/media/x7f2.mp4",
  "storage": true
}
```

---

## کلاینت مرورگر

npm: [`@pinooxhq/pinion-client`](https://www.npmjs.com/package/@pinooxhq/pinion-client)

```javascript
import { pinion } from '@pinooxhq/pinion-client';

await pinion({ baseURL: '/app/pinion', unwrapPreset: 'pinoox' })
  .for(file)
  .upload({ parallel: 2, onProgress: (p) => console.log(p.percent) });
```

راهنمای کامل: [client/README.md](../../pinion/client/README.md)

---

## CLI

```bash
php pinoox pinion:list
php pinoox pinion:info {upload_id}
php pinoox pinion:clean --abort={upload_id}
```

پروژه تک‌اپ — [Pinx CLI](../start/pinx-cli.md):

```bash
pinx pinion:list
pinx pinion:info {upload_id}
pinx pinion:clean
```

---

## یکپارچه‌سازی منجر

| Endpoint | کار |
|----------|-----|
| `POST /app/pinion/init` | شروع / resume |
| `POST /app/pinion/upload` | یک part |
| `POST /app/pinion/complete` | مونتاژ |
| `GET /app/pinion/status/{id}` | پیشرفت |
| `POST /app/pinion/abort/{id}` | لغو |

فرانت: `theme/spark/src/utils/pinion.js` — آستانه ۸ مگابایت.

---

## مستندات مرتبط

- [مدیریت فایل](./file-management.md)
- [README پکیج](../../pinion/README.md)
- [Pinx CLI](../start/pinx-cli.md)
- [مرجع CLI](../start/cli-reference.md)

---

[← بازگشت به فهرست](../README.md)
