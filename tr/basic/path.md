# Dosya yolu

[← Dizine dön](../README.md)

Diskteki dosya ve klasörlere erişmek için **`path()`** ve **`Pinoox\Portal\Path`** Portal'ını kullanın. Bu, kodun projenin nereye kurulduğundan ve `apps/` klasörünün ne adlandırıldığından bağımsız kalmasını sağlar.

---

## Standart yaklaşım — `path()`

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

## Yaygın kullanımlar

### Dosya okuma / yazma

```php
$file = path('storage/logs/app.log');
file_put_contents($file, $line, FILE_APPEND);
```

### Çeviri dosyası yolu

```php
$langFile = path('lang/en/welcome.lang.php');
```

### Tema yolu

```php
$themeDir = path('theme/default');
```

---

## Portal — `Path::get()`

`path()` ile aynı davranış, açık API ile:

```php
use Pinoox\Portal\Path;

Path::get('database/migrations');
Path::app();                    // current app
Path::app('com_pinoox_manager'); // specific app
```

---

## `path()` ve `url()`

| Helper | Çıktı | Örnek |
|--------|--------|---------|
| `path()` | Sunucudaki fiziksel yol | `/var/www/pinoox/apps/com_acme_shop/storage` |
| `url()` | Tarayıcı için HTTP URL'si | `https://site.com/pinoox/shop/products` |

---

## Örnek: yükleme servisi

Yüklemeleri `path()` + `move_uploaded_file()` ile manuel yazmayın — dosyaların proje `storage/` klasörüne düşmesi için **`File`** portal'ını kullanın:

```php
// apps/com_acme_shop/Component/UploadService.php
namespace App\com_acme_shop\Component;

use Pinoox\Portal\File;

class UploadService
{
    public function store($file, string $subdir = 'products'): ?string
    {
        // stored under storage/local/com_acme_shop/{subdir}
        $result = File::upload($file)
            ->to($subdir)
            ->diskOnly()
            ->save();

        return $result->success ? $result->path : null;
    }
}
```

Tam yükleme API'si için bkz. [Dosya yönetimi](../advanced/file-management.md).

---

## İpuçları

- Tarayıcıdan erişilebilir yollar için `path()` değil `url()` veya `assets()` kullanın.
- Paket adını yalnızca aktif olmayan bir uygulamaya ihtiyaç duyduğunuzda geçirin.
- Yol segmentlerini `/` ile birleştirin; Path doğru OS ayırıcısını halleder.

---

## İlgili dokümantasyon

- [URL ve bağlantılar](./url.md)
- [Config](./config.md)
- [Uygulama servisleri](../advanced/services.md)
- [Helper'lar](../advanced/helpers.md)

---

[← Dizine dön](../README.md)
