# Tests console dans Pinoox

[← Retour à l'index](../README.md)

Pour tester les commandes CLI Pinoox (`php pinoox ...`), utilisez `Symfony\Component\Process\Process` dans les tests Pest. Assertez la sortie et les codes de sortie — l'approche recommandée pour les tests terminal.

---

## Prérequis

Symfony Console Process est déjà disponible dans les dépendances du projet. Écrivez les tests dans les dossiers `Feature` ou `Unit` de l'app ou du cœur.

---

## Tester la commande migrate

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

## Tester une commande d'app personnalisée

Les commandes d'app vivent dans `apps/{package}/Terminal/` et sont découvertes via `php pinoox` :

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

## Tester un code de sortie d'échec

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

## Commandes interactives — les éviter

Pour les commandes qui demandent une saisie utilisateur, passez tous les arguments dans les tests pour qu'elles ne s'exécutent pas en mode interactif :

```bash
# ✅ dans les tests
php pinoox migrate com_my_shop

# ❌ dans les tests — attend la sélection utilisateur
php pinoox migrate
```

---

## Exécuter les tests

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## Conseils

1. Pointez `$root` vers la racine du projet (où se trouvent `pinoox` et `index.php`).
2. Définissez un long timeout pour migrate en CI : `$process->setTimeout(120)`.
3. Pour la logique pure dans une classe Command, utilisez un **test Unit** avec dépendances mockées ; Process est réservé à l'intégration CLI bout en bout.

---

## Documentation associée

- [Premiers pas avec les tests](./getting-started.md)
- [Mocking](./mocking.md)
- [Migrations](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Structure du projet](../start/structure.md)

---

[← Retour à l'index](../README.md)
