# Migrations

[← 색인으로 돌아가기](../README.md)

Migration은 database **schema** 변경을 버전 관리합니다. Pinoox 3.x에서 앱 file은 `apps/{package}/database/migrations/`에, core file은 `system/database/migrations/`에 있습니다.

---

## Migration 생성

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Output:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## File 구조

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

`$this->table('posts', $package)`가 올바른 앱 prefix를 적용합니다.

---

## Migration 실행

```bash
# app migration
php pinoox migrate com_acme_blog

# core migration
php pinoox migrate pincore

# platform migration (pinx_* tables)
php pinoox migrate platform
```

---

## Status와 rollback

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Core migration (예제)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Core table: prefix **`pincore_`** (또는 platform scope는 `pinx_`).

---

## Namespace

| Location | Namespace |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Core | `Pinoox\Database\migrations` |

---

## Legacy path

Pinoox는 여전히 구 `apps/{package}/migrations/` 폴더를 읽지만 **새** file은 `database/migrations/`에 생성됩니다.

---

## Migration vs Seed vs Patch

| Type | Purpose | Command |
|------|---------|---------|
| Migration | Schema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Initial data | `php pinoox seeder:run {package}` |
| Patch | One-time data change | `php pinoox patch:run {package}` |

Patch 전체 가이드: [Patches (data updates)](../advanced/patches.md).

---

## Best practices

- migration당 하나의 논리적 변경 (한 table 또는 한 ALTER).
- 항상 `down()` 작성.
- 이미 실행된 migration은 편집하지 말고 새로 생성.
- core table foreign key는 `$this->table(Table::FILE, 'platform')` 사용.

---

## 관련 문서

- [Database 시작하기](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← 색인으로 돌아가기](../README.md)
