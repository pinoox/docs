# Patches (atualizações de dados)

[← Voltar ao índice](../README.md)

Um **patch** no Pinoox 3.x é uma **alteração operacional única**: corrigir dados, mover registros, sincronizar config ou executar lógica pós-atualização. Não é **migration** (schema) nem **seeder** (dados de seed repetíveis).

---

## Quando usar um patch

| Ferramenta | Propósito |
|------|---------|
| **Migration** | CREATE/ALTER de tabelas e colunas |
| **Seeder** | Dados iniciais ou de exemplo (execuções manuais) |
| **Patch** | Executar uma vez e registrar em `history` |

Exemplos de patch:

- Corrigir linhas inválidas após um bug
- Preencher padrões em registros antigos
- Renomear valores de config no DB
- Lógica pós-atualização após nova release

---

## Localização dos arquivos

```text
vendor/pinoox/pincore/patches/     ← plataforma (CLI: platform)
apps/{package}/patches/            ← seu app
```

> O caminho legado `database/patches/` **não é usado**. Patches ficam ao lado de `app.php`, não em `database/`.

---

## Criar um patch

```bash
php pinoox patch:create fix_contact_status com_acme_shop
php pinoox patch:create rename_scope platform
```

A CLI grava um arquivo com timestamp, por exemplo:

```text
apps/com_acme_shop/patches/2026_06_10_143000_fix_contact_status.php
```

Formato do stub (classe anônima):

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

Namespace da plataforma: `Pinoox\Patches`.

---

## Métodos do PatchBase

| Método | Papel |
|--------|------|
| `up()` | Lógica principal (chamada via `run()`) |
| `down()` | Reverter quando `canRollback()` é true |
| `shouldRun()` | Se false, o patch é registrado como **skipped** |
| `canRollback()` | Se rollback é permitido |
| `description()` | Texto legível no history |
| `metadata()` | JSON extra armazenado no history |

---

## Comandos CLI

```bash
php pinoox patch:run com_acme_shop
php pinoox patch:run platform
php pinoox patch:status com_acme_shop
php pinoox patch:run com_acme_shop --class=2026_06_10_143000_fix_contact_status
php pinoox patch:rollback 2026_06_10_143000_fix_contact_status com_acme_shop
```

**Nota:** `patch:run` executa **migrations da plataforma** primeiro, depois patches do pacote selecionado.

Alias: `php pinoox patch` = `patch:run`.

---

## Tabela history

Migrations e patches compartilham a tabela **`history`**:

```text
type = migration | patch
app  = platform | com_acme_shop
status = success | failed | skipped | rolled_back
```

Patches bem-sucedidos não são reexecutados automaticamente.

---

## Instalador

O app de sistema `com_pinoox_installer` executa migrations e patches durante a instalação via `SetupService`.

---

## Boas práticas

- Não edite um patch que já foi executado — crie um novo.
- Use migrations para schema, não patches.
- Implemente `shouldRun()` para que verificações idempotentes ignorem trabalho desnecessário.
- Habilite rollback apenas quando `down()` for seguro.

---

## Documentação relacionada

- [Migrations](./migrations.md)
- [Seeders / factories](../eloquent-orm/factories.md)
- [Referência CLI](../start/cli-reference.md)

---

[← Voltar ao índice](../README.md)
