# Testes de console no Pinoox

[← Voltar ao índice](../README.md)

Para testar comandos CLI do Pinoox (`php pinoox ...`), use `Symfony\Component\Process\Process` em testes Pest. Asserte saída e códigos de saída — a abordagem recomendada para testes de terminal.

---

## Pré-requisitos

Symfony Console Process já está disponível nas dependências do projeto. Escreva testes nas pastas `Feature` ou `Unit` do app ou do núcleo.

---

## Testar o comando migrate

```php
// apps/com_my_shop/tests/Feature/MigrateCommandTest.php

use Symfony\Component\Process\Process;

it('runs migrate for the app', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', appPackage()],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeTrue()
        ->and($process->getOutput())->toContain('Migrated');
});
```

---

## Testar um comando personalizado do app

Comandos de app ficam em `apps/{package}/Terminal/` e são descobertos via `php pinoox`:

```php
it('runs custom report command', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'report:daily', '-p', appPackage()],
        $root
    );

    $process->run();

    expect($process->getExitCode())->toBe(0);
});
```

---

## Testar saída de falha

```php
it('fails when package is missing', function () {
    $root = dirname(__DIR__, 4);

    $process = new Process(
        ['php', 'pinoox', 'migrate', 'com_nonexistent'],
        $root
    );

    $process->run();

    expect($process->isSuccessful())->toBeFalse();
});
```

---

## Comandos interativos — evite-os

Para comandos que pedem entrada do usuário, passe argumentos completos nos testes para não rodar interativamente:

```bash
# ✅ nos testes
php pinoox migrate com_my_shop

# ❌ nos testes — aguarda seleção do usuário
php pinoox migrate
```

---

## Executar testes

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## Dicas

1. Aponte `$root` para a raiz do projeto (onde ficam `pinoox` e `index.php`).
2. Defina timeout longo para migrate no CI: `$process->setTimeout(120)`.
3. Para lógica pura dentro de uma classe Command, use **teste Unit** com dependências mockadas; Process é só para integração CLI end-to-end.

---

## Documentação relacionada

- [Primeiros passos com testes](./getting-started.md)
- [Mocking](./mocking.md)
- [Migrations](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Estrutura do projeto](../start/structure.md)

---

[← Voltar ao índice](../README.md)
