# Test Data — Seeders

[← 색인으로 돌아가기](../README.md)

Pinoox 3.x CLI에는 **Model Factory**(Laravel-style)가 포함되어 있지 않습니다. 초기 및 development data의 권장 방법은 `apps/{package}/database/seed/`의 **`SeederBase`**를 사용하는 **Seeder**입니다.

---

## Seeder 생성

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seed/PostSeeder.php
```

---

## 구조

```php
<?php
namespace App\com_acme_blog\database\seed;

use App\com_acme_blog\Model\PostModel;
use Pinoox\Component\Database\Seeder\SeederBase;
use Pinoox\Portal\Hash;

return new class extends SeederBase
{
    public function run(): void
    {
        PostModel::insert([
            [
                'user_id' => 1,
                'title' => 'First post',
                'body' => 'Sample content',
                'status' => 'published',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => 1,
                'title' => 'Second post',
                'body' => '...',
                'status' => 'draft',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
};
```

---

## 다른 seeder 호출

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
    ]);

    // dependent data after users
    PostModel::factory(); // ❌ not available — use insert or create manually
}
```

---

## Model로 create

```php
for ($i = 1; $i <= 20; $i++) {
    PostModel::create([
        'user_id' => 1,
        'title' => "Post #{$i}",
        'body' => 'Lorem ipsum',
        'status' => $i % 2 ? 'published' : 'draft',
    ]);
}
```

---

## Seeder 실행

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## 권장 순서

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Production의 Seeder

- **필수** data만 (role, default 설정).
- `APP_ENV`로 fake/dev data 보호:

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // sample data
}
```

---

## Seeder vs Patch

| Seeder | Patch |
|--------|-------|
| Initial / sample data | 기존 data 일회성 수정 |
| `seeder:run` — 주의하여 반복 가능 | `patch:run` — 한 번 추적 |

---

## Tips

- idempotent seeder 작성 (무분별 `insert` 대신 `firstOrCreate`).
- seeder에 실제 credential commit 금지.
- unit test에는 Pest fixture 또는 `:memory:` sqlite 사용.

---

## 관련 문서

- [Migrations](../database/migrations.md)
- [Eloquent 시작하기](./getting-started.md)
- [App database configuration (app.php)](../start/app-manifest.md)

---

[← 색인으로 돌아가기](../README.md)
