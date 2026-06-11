# Primeiros passos com banco de dados

[← Voltar ao índice](../README.md)

O Pinoox 3.x fornece a camada de banco de dados por meio do **Illuminate Database** (Eloquent + Query Builder) e do portal **`Pinoox\Portal\Database\DB`**. Cada app define sua conexão em `app.php`; credenciais da plataforma ficam no `.env` do projeto.

---

## Portal DB

```php
use Pinoox\Portal\Database\DB;

DB::app()->table('orders')->get();                    // conexão do app ativo
DB::app('com_my_shop')->table('orders')->get();      // conexão de app específico
DB::core()->table('user')->get();                     // tabelas do pincore
DB::tableName('orders');                             // nome físico com prefixo
```

---

## Padrão da plataforma

```php
// app.php
'database' => null,
```

Models e queries usam a conexão padrão do projeto (`DB_CONNECTION` no `.env`).

---

## Conexão nomeada da plataforma

```php
'database' => [
    'use' => 'mariadb',
],
```

```env
# apps/{package}/.env
DB_USE=mariadb
```

O Pinoox clona uma conexão chamada `app_{package}_default` a partir do bloco da plataforma.

---

## Prefixo de tabela

### App em DB compartilhado (sem banco dedicado)

Padrão: prefixo curto derivado do nome do pacote.

```php
'database' => null,
// com_pinoox_manager + tabela notifications → manager_notifications
```

### Prefixo explícito

```php
'database' => [
    'use' => 'mysql',
    'prefix' => 'shop_',
],
// ou
'table' => [
    'prefix' => 'welcome_',
],
```

```env
DB_PREFIX=shop_
```

### DB dedicado — sem prefixo

```php
'database' => [
    'driver' => 'mysql',
    'database' => 'myshop',
    'table_prefix' => '',
],
// notifications → notifications
```

### Tabelas do núcleo

Sempre prefixo **`pincore_`**: `pincore_user`, `pincore_token`, `pincore_file`.

---

## Banco de dados dedicado completo

```php
'database' => [
    'driver' => 'sqlite',
    'database' => storage_path('apps/myshop/database.sqlite'),
    'prefix' => '',
],
```

Várias conexões:

```php
'database' => [
    'default' => 'primary',
    'connections' => [
        'primary' => ['driver' => 'sqlite', 'database' => ':memory:'],
        'analytics' => ['use' => 'mysql', 'prefix' => 'an_'],
    ],
],
```

```php
DB::app('com_my_shop', 'analytics')->table('events')->get();
```

---

## Chaves do .env do app

| Chave | Mapeia para |
|-----|---------|
| `DB_USE` | `database.use` |
| `DB_PREFIX` | `database.prefix` |
| `DB_DRIVER` | `database.driver` |
| `DB_HOST` | `database.host` |
| `DB_DATABASE` | `database.database` |
| `DB_USERNAME` / `DB_PASSWORD` | credenciais dedicadas |

**Não** use `DB_CONNECTION` no `.env` do app — ele é ignorado.

---

## Layout da pasta database

```text
apps/{package}/
├── patches/                 ← patches de dados únicos
└── database/
    migrations/
    seed/
```

---

## Resolver nomes de tabela

```php
DB::tableName('notifications', 'com_pinoox_manager');
DB::tablePrefixForPackage('com_pinoox_manager');
DB::physicalTableName('orders');
```

---

## Dicas

- Mantenha lógica de negócio em Model/Component; controllers permanecem enxutos.
- Migrations e seeds ficam apenas na pasta `database/` do app — não no pincore.
- O Pinker pode sobrescrever `database.use` e `database.prefix`.

---

## Documentação relacionada

- [Query Builder](./query-builder.md)
- [Migrations](./migrations.md)
- [Eloquent — primeiros passos](../eloquent-orm/getting-started.md)
- [Configuração de banco no app.php](../start/app-manifest.md)

---

[← Voltar ao índice](../README.md)
