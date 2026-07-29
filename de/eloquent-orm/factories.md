# Testdaten — Seeder

[← Zurück zum Index](../README.md)

Pinoox 3.x enthält keine **Model Factory** (Laravel-Stil) in der CLI. Der empfohlene Ansatz für Anfangs- und Entwicklungsdaten sind **Seeder** mit `SeederBase` in `apps/{package}/database/seeders/`.

---

## Seeder erstellen

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## Struktur

```php
<?php
namespace App\com_acme_blog\database\seeders;

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

## Anderen Seeder aufrufen

```php
public function run(): void
{
    $this->call([
        'RoleSeeder',
        'UserSeeder',
    ]);

    // abhängige Daten nach Benutzern
    PostModel::factory(); // ❌ nicht verfügbar — insert oder create manuell verwenden
}
```

---

## create mit Model

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

## Seeder ausführen

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
php pinoox seeder:run platform
```

`-c` matches the **file basename** (e.g. `PostSeeder`). App install does **not** auto-run seeders.

---

## Call Seeders From Code

Use `Pinoox\Portal\Database\Seeder`, or `$this->seed()` / `$this->seedAll()` in migrations and patches.

```php
use Pinoox\Portal\Database\Seeder;

Seeder::run('PostSeeder');
Seeder::run('PostSeeder', 'com_acme_blog');
Seeder::run(['RoleSeeder', 'PostSeeder']);
Seeder::runAll();
Seeder::runAll('platform');
```

```php
// migration / patch
$this->seed('GatewaySeeder');
$this->seedAll();
```

```php
// from another seeder
$this->call(['RoleSeeder', 'UserSeeder']);
```

See English docs: [Factories and seeders](../../en/eloquent-orm/factories.md#call-seeders-from-code).

---

## Empfohlene Reihenfolge

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeder in der Produktion

- Nur **wesentliche** Daten (Rollen, Standardeinstellungen).
- Fake-/Dev-Daten mit `APP_ENV` absichern:

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // Beispieldaten
}
```

---

## Seeder vs. Patch

| Seeder | Patch |
|--------|-------|
| Anfangs- / Beispieldaten | Einmalige Korrektur bestehender Daten |
| `seeder:run` — wiederholbar mit Vorsicht | `patch:run` — einmal verfolgt |

---

## Tipps

- Idempotente Seeder schreiben (`firstOrCreate` statt blindem `insert`).
- Keine echten Zugangsdaten in Seedern committen.
- Für Unit-Tests Pest-Fixtures oder `:memory:` sqlite verwenden.

---

## Verwandte Dokumentation

- [Migrationen](../database/migrations.md)
- [Eloquent — Erste Schritte](./getting-started.md)
- [App-Datenbankkonfiguration (app.php)](../start/app-manifest.md)

---

[← Zurück zum Index](../README.md)
