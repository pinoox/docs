# Patch'ler (veri güncellemeleri)

[← Dizine dön](../README.md)

Pinoox 3.x'te **patch**, **tek seferlik operasyonel değişikliktir**: veri düzeltme, kayıt taşıma, config senkronizasyonu veya yükseltme sonrası mantık. **Migration** (şema) veya **seeder** (tekrarlanabilir seed verisi) değildir.

---

## Patch ne zaman kullanılır

| Araç | Amaç |
|------|---------|
| **Migration** | CREATE/ALTER tablolar ve sütunlar |
| **Seeder** | Başlangıç veya örnek veri (manuel çalıştırma) |
| **Patch** | Bir kez çalıştır ve `history` içinde izle |

Patch örnekleri:

- Bir hatadan sonra geçersiz satırları düzeltme
- Eski kayıtlar için varsayılanları doldurma
- DB'de config değerlerini yeniden adlandırma
- Yeni sürümden sonra güncelleme sonrası mantık

---

## Dosya konumları

```text
vendor/pinoox/pincore/patches/     ← platform (CLI: platform)
apps/{package}/patches/            ← your app
```

> Eski yol `database/patches/` **kullanılmaz**. Patch'ler `app.php` yanında yer alır, `database/` altında değil.

---

## Patch oluşturma

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

CLI zaman damgalı bir dosya yazar, ör.:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

İskelet şekli (anonim sınıf):

```php
<?php
namespace App\com_acme_shop\patches;

use Pinoox\Component\Database\Patch\PatchBase;
use Pinoox\Portal\Database\DB;

return new class extends PatchBase
{
    public function description(): string
    {
        return 'Set empty contact status to active';
    }

    public function shouldRun(): bool
    {
        return DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->exists();
    }

    public function canRollback(): bool
    {
        return false;
    }

    public function up(): void
    {
        DB::table(DB::tableName('contacts', 'com_acme_shop'))
            ->whereNull('status')
            ->update(['status' => 'active']);
    }
}
```

Platform namespace: `Pinoox\Patches`.

---

## PatchBase metotları

| Metot | Rol |
|--------|------|
| `up()` | Ana mantık (`run()` üzerinden çağrılır) |
| `down()` | `canRollback()` true olduğunda geri al |
| `shouldRun()` | false ise patch **skipped** olarak kaydedilir |
| `canRollback()` | Geri almanın izinli olup olmadığı |
| `description()` | history'de okunabilir metin |
| `metadata()` | history'de saklanan ek JSON |

---

## CLI komutları

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Not:** `patch:run` önce **platform migration'larını** çalıştırır, ardından seçilen paket için patch'leri.

Takma ad: `php pinoox patch` = `patch:run`.

---

## history tablosu

Migration'lar ve patch'ler **`history`** tablosunu paylaşır:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Başarılı patch'ler otomatik olarak yeniden çalıştırılmaz.

---

## Yükleyici

Sistem uygulaması `com_pinoox_installer`, kurulum sırasında `SetupService` üzerinden migration'ları ve patch'leri çalıştırır.

---

## En iyi uygulamalar

- Zaten çalışmış bir patch'i düzenlemeyin — yeni bir tane oluşturun.
- Şema için migration kullanın, patch değil.
- Gereksiz işi atlamak için idempotent kontrollerle `shouldRun()` uygulayın.
- Rollback'i yalnızca `down()` güvenli olduğunda etkinleştirin.

---

## İlgili dokümantasyon

- [Migration'lar](./migrations.md)
- [Seeder'lar / factory'ler](../eloquent-orm/factories.md)
- [CLI referansı](../start/cli-reference.md)

---

[← Dizine dön](../README.md)
