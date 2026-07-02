# Dados de teste — Seeders

[← Voltar ao índice](../README.md)

O Pinoox 3.x não inclui **Model Factory** (estilo Laravel) na CLI. A abordagem recomendada para dados iniciais e de desenvolvimento são **Seeders** com `SeederBase` em `apps/{package}/database/seeders/`.

---

## Criar um seeder

```bash
php pinoox seeder:create PostSeeder com_acme_blog
```

```text
apps/com_acme_blog/database/seeders/PostSeeder.php
```

---

## Estrutura

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

## Chamar outro seeder

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        UserSeeder::class,
    ]);

    // dados dependentes após users
    PostModel::factory(); // ❌ não disponível — use insert ou create manualmente
}
```

---

## create com Model

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

## Executar seeders

```bash
php pinoox seeder:run com_acme_blog
php pinoox seeder:run com_acme_blog --class=PostSeeder
php pinoox seeder:run com_acme_blog -c PostSeeder
```

---

## Ordem recomendada

1. `php pinoox migrate com_acme_blog`
2. `php pinoox seeder:run com_acme_blog`

---

## Seeders em produção

- Apenas dados **essenciais** (roles, configurações padrão).
- Proteja dados fake/dev com `APP_ENV`:

```php
public function run(): void
{
    if (env('APP_ENV') === 'production') {
        return;
    }
    // dados de exemplo
}
```

---

## Seeder vs Patch

| Seeder | Patch |
|--------|-------|
| Dados iniciais / de exemplo | Correção única em dados existentes |
| `seeder:run` — repetível com cautela | `patch:run` — rastreado uma vez |

---

## Dicas

- Escreva seeders idempotentes (`firstOrCreate` em vez de `insert` cego).
- Não faça commit de credenciais reais em seeders.
- Para testes unitários, use fixtures Pest ou sqlite `:memory:`.

---

## Documentação relacionada

- [Migrations](../database/migrations.md)
- [Primeiros passos com Eloquent](./getting-started.md)
- [Configuração de banco no app.php](../start/app-manifest.md)

---

[← Voltar ao índice](../README.md)
