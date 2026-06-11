# Migration'lar

[← Dizine dön](../README.md)

Migration'lar veritabanındaki **şema** değişikliklerini sürümlendirir. Pinoox 3.x'te uygulama dosyaları `apps/{package}/database/migrations/` içinde, çekirdek dosyalar `system/database/migrations/` içinde yer alır.

---

## Migration oluşturma

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Çıktı:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## Dosya yapısı

```php
<?php
namespace App\com_acme_blog\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('posts', 'com_acme_blog'), function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('title', 255);
            $table->text('body')->nullable();
            $table->string('status', 50)->default('draft');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('posts', 'com_acme_blog'));
    }
};
```

`$this->table('posts', $package)` doğru uygulama önekini uygular.

---

## Migration'ları çalıştırma

```bash
# app migration
php pinoox migrate com_acme_blog

# core migration
php pinoox migrate pincore

# platform migration (pinx_* tables)
php pinoox migrate platform
```

---

## Durum ve geri alma

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Çekirdek migration (örnek)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Çekirdek tablolar: **`pincore_`** öneki (veya platform kapsamı için `pinx_`).

---

## Namespace'ler

| Konum | Namespace |
|----------|-----------|
| Uygulama | `App\{package}\database\migrations` |
| Çekirdek | `Pinoox\Database\migrations` |

---

## Eski yol

Pinoox hâlâ eski `apps/{package}/migrations/` klasörünü okur, ancak **yeni** dosyalar `database/migrations/` içinde oluşturulur.

---

## Migration vs Seed vs Patch

| Tür | Amaç | Komut |
|------|---------|---------|
| Migration | Şema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Başlangıç verisi | `php pinoox seeder:run {package}` |
| Patch | Tek seferlik veri değişikliği | `php pinoox patch:run {package}` |

Tam patch rehberi: [Patch'ler (veri güncellemeleri)](./patches.md).

---

## En iyi uygulamalar

- Migration başına tek mantıksal değişiklik (bir tablo veya bir ALTER).
- Her zaman `down()` yazın.
- Zaten çalışmış bir migration'ı düzenlemeyin — yeni bir tane oluşturun.
- Çekirdek tablolara foreign key'ler `$this->table(Table::FILE, 'platform')` kullanır.

---

## İlgili dokümantasyon

- [Veritabanına başlarken](./getting-started.md)
- [Seeder'lar / factory'ler](../eloquent-orm/factories.md)
- [Uygulama veritabanı yapılandırması (app.php)](../start/app-manifest.md)

---

[← Dizine dön](../README.md)
