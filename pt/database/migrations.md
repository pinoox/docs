# Migrations

[← Voltar ao índice](../README.md)

Migrations versionam alterações de **schema** no banco de dados. No Pinoox 3.x, arquivos do app ficam em `apps/{package}/database/migrations/` e arquivos do núcleo em `system/database/migrations/`.

---

## Criar uma migration

```bash
php pinoox migrate:create CreatePosts com_acme_blog
```

Saída:

```text
apps/com_acme_blog/database/migrations/2026_06_10_120000_create_posts_table.php
```

---

## Estrutura do arquivo

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

`$this->table('posts', $package)` aplica o prefixo correto do app.

---

## Executar migrations

```bash
# migration do app
php pinoox migrate com_acme_blog

# migration do núcleo
php pinoox migrate pincore

# migration da plataforma (tabelas pinx_*)
php pinoox migrate platform
```

---

## Status e rollback

```bash
php pinoox migrate:status com_acme_blog
php pinoox migrate:rollback com_acme_blog
php pinoox migrate:rollback com_acme_blog --step=1
```

---

## Migration do núcleo (exemplo)

```php
namespace Pinoox\Database\migrations;

use Pinoox\Model\Table;

$this->schema->create($this->table(Table::USER, 'platform'), function (Blueprint $table) {
    $table->increments('user_id');
    // ...
});
```

Tabelas do núcleo: prefixo **`pincore_`** (ou `pinx_` para escopo platform).

---

## Namespaces

| Local | Namespace |
|----------|-----------|
| App | `App\{package}\database\migrations` |
| Núcleo | `Pinoox\Database\migrations` |

---

## Caminho legado

O Pinoox ainda lê a pasta antiga `apps/{package}/migrations/`, mas arquivos **novos** são criados em `database/migrations/`.

---

## Migration vs Seed vs Patch

| Tipo | Propósito | Comando |
|------|---------|---------|
| Migration | Schema (CREATE/ALTER) | `php pinoox migrate {package}` |
| Seeder | Dados iniciais | `php pinoox seeder:run {package}` |
| Patch | Alteração de dados única | `php pinoox patch:run {package}` |

Guia completo de patches: [Patches (atualizações de dados)](./patches.md).

---

## Boas práticas

- Uma alteração lógica por migration (uma tabela ou um ALTER).
- Sempre escreva `down()`.
- Não edite uma migration que já foi executada — crie uma nova.
- Chaves estrangeiras para tabelas do núcleo usam `$this->table(Table::FILE, 'platform')`.

---

## Documentação relacionada

- [Primeiros passos com banco de dados](./getting-started.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [Configuração de banco no app.php](../start/app-manifest.md)

---

[← Voltar ao índice](../README.md)
