# Konsolen-Tests in Pinoox

[← Zurück zum Index](../README.md)

Zum Testen von Pinoox-CLI-Befehlen (`php pinoox ...`) `Symfony\Component\Process\Process` in Pest-Tests verwenden. Ausgabe und Exit-Codes prüfen — der empfohlene Ansatz für Terminal-Tests.

---

## Voraussetzungen

Symfony Console Process ist in den Projektabhängigkeiten bereits verfügbar. Tests in App- oder Core-Ordnern `Feature` oder `Unit` schreiben.

---

## migrate-Befehl testen

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

## Eigenen App-Befehl testen

App-Befehle liegen in `apps/{package}/Terminal/` und werden über `php pinoox` gefunden:

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

## Fehler-Exit testen

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

## Interaktive Befehle — vermeiden

Bei Befehlen mit Benutzereingabe in Tests vollständige Argumente übergeben, damit sie nicht interaktiv laufen:

```bash
# ✅ in Tests
php pinoox migrate com_my_shop

# ❌ in Tests — wartet auf Benutzerauswahl
php pinoox migrate
```

---

## Tests ausführen

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## Tipps

1. `$root` auf das Projektroot zeigen lassen (wo `pinoox` und `index.php` liegen).
2. Für migrate in CI langes Timeout setzen: `$process->setTimeout(120)`.
3. Für reine Logik innerhalb einer Command-Klasse einen **Unit-Test** mit gemockten Abhängigkeiten; Process nur für End-to-End-CLI-Integration.

---

## Verwandte Dokumentation

- [Erste Schritte beim Testen](./getting-started.md)
- [Mocking](./mocking.md)
- [Migrationen](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zum Index](../README.md)
