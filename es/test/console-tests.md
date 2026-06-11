# Tests de consola en Pinoox

[← Volver al índice](../README.md)

Para probar comandos CLI de Pinoox (`php pinoox ...`), usa `Symfony\Component\Process\Process` en tests Pest. Comprueba salida y códigos de salida — el enfoque recomendado para testing de terminal.

---

## Requisitos previos

Symfony Console Process ya está disponible en las dependencias del proyecto. Escribe tests en carpetas `Feature` o `Unit` de app o núcleo.

---

## Probar el comando migrate

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

## Probar un comando personalizado de la app

Los comandos de app viven en `apps/{package}/Terminal/` y se descubren vía `php pinoox`:

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

## Probar salida de error

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

## Comandos interactivos — evítalos

Para comandos que piden entrada al usuario, pasa argumentos completos en tests para que no se ejecuten de forma interactiva:

```bash
# ✅ en tests
php pinoox migrate com_my_shop

# ❌ en tests — espera selección del usuario
php pinoox migrate
```

---

## Ejecutar tests

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## Consejos

1. Apunta `$root` a la raíz del proyecto (donde viven `pinoox` e `index.php`).
2. Establece timeout largo para migrate en CI: `$process->setTimeout(120)`.
3. Para lógica pura dentro de una clase Command, usa un **test Unit** con dependencias mockeadas; Process es solo para integración CLI end-to-end.

---

## Documentación relacionada

- [Primeros pasos con testing](./getting-started.md)
- [Mocking](./mocking.md)
- [Migraciones](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Estructura del proyecto](../start/structure.md)

---

[← Volver al índice](../README.md)
