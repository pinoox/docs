# Pinoox'ta konsol testleri

[← Dizine dön](../README.md)

Pinoox CLI komutlarını (`php pinoox ...`) test etmek için Pest testlerinde `Symfony\Component\Process\Process` kullanın. Çıktı ve çıkış kodlarını doğrulayın — terminal testi için önerilen yaklaşım budur.

---

## Ön koşullar

Symfony Console Process proje bağımlılıklarında zaten mevcuttur. Testleri uygulama veya çekirdek `Feature` veya `Unit` klasörlerine yazın.

---

## migrate komutunu test etme

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

## Özel uygulama komutunu test etme

Uygulama komutları `apps/{package}/Terminal/` içinde yer alır ve `php pinoox` üzerinden keşfedilir:

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

## Başarısız çıkışı test etme

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

## Etkileşimli komutlar — kaçının

Kullanıcı girdisi isteyen komutlarda testlerde etkileşimli çalışmaması için tam argümanları geçirin:

```bash
# ✅ in tests
php pinoox migrate com_my_shop

# ❌ in tests — waits for user selection
php pinoox migrate
```

---

## Testleri çalıştırma

```bash
php pinoox test com_my_shop -f MigrateCommand
vendor/bin/pest --filter=MigrateCommand
```

---

## İpuçları

1. `$root`'u proje köküne işaret ettirin (`pinoox` ve `index.php` burada).
2. CI'da migrate için uzun zaman aşımı ayarlayın: `$process->setTimeout(120)`.
3. Command sınıfı içindeki saf mantık için mock bağımlılıklarla **Unit test** kullanın; Process yalnızca uçtan uca CLI entegrasyonu içindir.

---

## İlgili dokümantasyon

- [Teste başlarken](./getting-started.md)
- [Mocking](./mocking.md)
- [Migration'lar](../database/migrations.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Proje yapısı](../start/structure.md)

---

[← Dizine dön](../README.md)
