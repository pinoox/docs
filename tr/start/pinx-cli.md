# Pinx CLI (tek uygulamalı projeler)

[← Dizine dön](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)**, **tek uygulamalı** Pinoox projeleri için geliştirici CLI'sidir — çok uygulamalı bir manager'a dokunmadan iskelet oluşturma, çalıştırma, migration, derleme ve `.pinx` paketleri dağıtma.

`pinoox/pincore` ve `pinoox/app` şablonu üzerine kuruludur. Proje kökünüz **uygulamanın kendisidir**: tek `app.php`, tek paket, tek iş akışı.

> Klasik çok uygulamalı platform kurulumları için bunun yerine [`php pinoox`](./cli-reference.md) kullanın.

---

## Hızlı başlangıç

Pinx'i bir kez kurun, yeni uygulama oluşturun ve çalıştırın:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # suggests com_my_shop — confirm or edit in the wizard
cd my-shop
cp .env.example .env          # set DB_* if you use a database
pinx setup                    # migrate platform + app, run seeders
pinx dev                      # http://127.0.0.1:8000
```

`pinx` bulunamazsa Composer'ın global `bin` dizinini `PATH`'e ekleyin:

- Linux / macOS: `~/.composer/vendor/bin` veya `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| Adım | Ne yapar |
|------|--------------|
| `composer global require` | Makinenize `pinx` komutunu kurar |
| `pinx new my-shop` | `pinoox/app`'ten iskelet oluşturur; sihirbaz 3 parçalı paket önerir (ör. `com_my_shop`) |
| `.env` | Veritabanı ve proje yolları — `.env.example`'dan kopyalayın |
| `pinx setup` | Tek seferde: platform migration'ları → uygulama migration'ları → seeder'lar |
| `pinx dev` | PHP dev sunucusu; frontend stack yapılandırıldığında Vite'i de başlatır |

Paket adları `com_{vendor}_{name}` formatını izler — ör. `com_acme_shop`, `ir_yekdo_app`. Zaten boş bir klasörün içinde misiniz? `pinx new` yerine `pinx init` kullanın.

**`setup` öncesi isteğe bağlı kontrol:** `pinx doctor` PHP, düzen, env, DB ve derleme hazırlığını raporlar.

---

## Alternatif: `composer create-project`

Global kurulum yok — şablon proje içinde `bin/pinx` ile gelir:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## Tek uygulamayı farklı kılan nedir

Klasik Pinoox kurulumları birçok uygulamayı `apps/` altında tutar ve çalışma zamanında birini seçer. **Tek uygulama** bunu düzleştirir:

- Proje kökündeki `app.php` paket kimliğini ve pinx ayarlarını tutar
- `Controller/`, `Model/`, `routes/`, `theme/` kökte yer alır — `apps/{package}/` içinde değil
- `platform/` yerel routing ve launcher config'ini tutar (`.pinx` derlemelerinden hariç)
- Pinx her zaman **sizin** uygulamanızı hedefler — paket seçici yok, manager arayüzü yok

```
my-shop/                    ← proje kökü = uygulama kökü
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← dev host + deploy katmanı (yalnızca yerel)
├── bin/pinx                ← proje-yerel CLI girişi
└── vendor/pinoox/pincore   ← framework
```

---

## Kurulum seçenekleri

| Nerede | Nasıl | Ne zaman kullanılır |
|-------|-----|-------------|
| **Global** | `composer global require pinoox/pinx-cli` | Önerilen — her yerden `pinx new` ve `pinx init` |
| **Proje başına** | `pinoox/app` içinde `bin/pinx` olarak gelir | `composer create-project` sonrası — global kurulum gerekmez |

```bash
pinx -v          # CLI version (e.g. pinx-cli 1.1.7)
pinx list        # grouped command overview
pinx help setup  # detail for one command
```

---

## Günlük iş akışı

```bash
pinx dev                    # local server (+ Vite when app.php → frontend.stack is set)
pinx dev --open             # open browser after start
pinx dev --no-frontend      # PHP only

pinx migrate                # run app migrations (--platform runs platform first)
pinx migrate:st             # migration status
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # list named actions (--validate, --json)
pinx test                   # run app tests (Pest)
```

**Frontend** (`theme/` Vue/React + Vite kullandığında):

```bash
pinx fe:info                # stack, npm scripts, paths
pinx fe:i                   # npm install
pinx fe:d                   # Vite dev server
pinx fe:b                   # production build
pinx fe:sc --stack=vue      # scaffold starter files
```

**Bağımlılıklar:**

```bash
pinx deps:st                # Composer + npm status
pinx deps:i                 # install all
pinx deps:up                # update all
```

**Pinker** (derleme önbelleği):

```bash
pinx pinker:st              # cache vs source
pinx pinker:rb              # rebuild
pinx pinker:df              # diff
```

---

## Üretime dağıtım

Tam bir Pinoox platformuna kurulum için `.pinx` paketi oluşturun (Manager → Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # bump version in app.php + build
pinx release --sign         # sign when key is configured in app.php → pinx.sign
```

`pinx build` mantıklı varsayılanlar uygular (`vendor/`, `bin/`, `.env`, `platform/`, dev araçlarını hariç tutar). Yalnızca gerektiğinde `app.php` içinde geçersiz kılın:

```php
'build' => [
    'exclude' => ['my-private-notes/'],
    'composer' => false,
],
'pinx' => [
    'sign' => [
        'enabled' => false,
        'key' => null,
        'key_id' => null,
    ],
],
```

---

## `pinx doctor`

Doctor yapılandırılmış bir tanı çalıştırır ve bir şey başarısız olduğunda düzeltme komutları önerir:

| Grup | Kontroller |
|-------|--------|
| **Proje** | `app.php`, paket kimliği, `platform/` düzeni |
| **Runtime** | PHP sürümü (≥ 8.1), extension'lar, yazılabilir yollar |
| **Bağımlılıklar** | Composer vendor, isteğe bağlı Node/npm |
| **Ortam** | `.env` varlığı ve anahtar değişkenler |
| **Veritabanı** | Bağlantı (`--skip-db` ile atlanabilir) |
| **Frontend** | Tema stack'i, `package.json` (`--skip-frontend` ile atlanabilir) |
| **Derleme** | Export hazırlığı, ikon, sürüm alanları |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # CI-friendly report
pinx doctor --no-fixes      # hide suggested commands
```

---

## Komut referansı

Bölümlü genel bakış için `pinx list` çalıştırın. Kısaltma takma adları köşeli parantez içinde görünür.

### Proje

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `new` | — | `pinoox/app`'ten iskelet (sihirbaz veya bayraklar) |
| `init` | — | Mevcut dizini başlat (`--force` üzerine yazmak için) |
| `setup` | — | DB: platform + uygulama migrate, ardından seed |
| `doctor` | `dr` | Sağlık kontrolü — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | `app.php`'den meta veriyi göster |

### Geliştirme

| Komut | Açıklama |
|---------|-------------|
| `dev` | Dev sunucusu; `frontend.stack` vue/react olduğunda Vite |

### Veritabanı

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `migrate:run` | `migrate` | Uygulama migration'larını çalıştır (`--platform` önce platform'u çalıştırır) |
| `migrate:status` | `migrate:st` | Migration durumu |
| `migrate:rollback` | `migrate:rb` | Son batch'i geri al (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Migration dosyası oluştur |
| `migrate:platform` | `migrate:pl` | Yalnızca platform migration'ları |
| `seeder:run` | `seed` | Seeder'ları çalıştır (`-c` sınıf) |

### Patch'ler

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `patch:run` | `patch` | Bekleyen patch'leri çalıştır |
| `patch:status` | `patch:st` | Patch durumu |
| `patch:rollback` | `patch:rb` | Son patch batch'ini geri al |

### Derleme ve yayın

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `build` | `bld` | `.pinx` paketi oluştur |
| `release` | `rel` | Sürüm artırma + derleme (`--bump`, `--sign`) |

### İskelet oluşturma

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Route'lar

| Komut | Açıklama |
|---------|-------------|
| `route:actions` / `routes` | Named action'ları listele (`--validate`, `--json`) |

### Bağımlılıklar

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Composer + npm durumu |
| `deps:install` | `deps:i` | Bağımlılıkları kur |
| `deps:update` | `deps:up` | Bağımlılıkları güncelle |

### Frontend

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | Tema stack'i ve npm script'leri |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | Üretim derlemesi |
| `fe:dev` | `fe:d` | Vite dev sunucusu |
| `fe:scaffold` | `fe:sc` | Başlangıç dosyaları (`--stack=vue\|react\|twig`) |

### Zamanlama

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | `schedule.php`'den cron görevlerini listele |
| `schedule:run` | `sched:run` | Vadesi gelen görevleri çalıştır (`--dry-run`) |

### Pinion (devam ettirilebilir yüklemeler)

`php pinoox pinion:*` komutlarına iletilir — geçici parçalı yükleme oturumlarını yönetin.

| Komut | Açıklama |
|---------|-------------|
| `pinion:list` | List sessions (`--status`, `--json`) |
| `pinion:info {upload_id}` | Session detail |
| `pinion:clean` | Remove expired sessions (`--abort={id}`) |

```bash
pinx pinion:list --status=pending
pinx pinion:info {upload_id} --json
```

Bkz. [Pinion protokolü](../advanced/pinion.md).

### Pinker

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Önbellek ve kaynak karşılaştırması |
| `pinker:rebuild` | `pinker:rb` | Önbelleği yeniden oluştur |
| `pinker:diff` | `pinker:df` | Farkları göster |
| `pinker:clear` | `pinker:cl` | Önbelleği temizle |
| `pinker:overrides` | `pinker:ov` | Override'ları listele |

### Kalite ve dokümantasyon

| Komut | Açıklama |
|---------|-------------|
| `test` / `pest` | Uygulama testlerini çalıştır (`--unit`, `--feature`) |
| `api:docs` | REST API dokümantasyonu |
| `graphql:docs` | GraphQL şema dokümantasyonu |

### Meta

| Komut | Takma adlar | Açıklama |
|---------|---------|-------------|
| `list` | — | Gruplandırılmış komut genel bakışı |
| `version` | `ver` | CLI sürümü |

---

## Uygulama algılama

Pinx, geçerli bir tek uygulamalı proje bulana kadar mevcut çalışma dizininden yukarı doğru yürür:

1. `app.php` mevcut ve boş olmayan `package` anahtarıyla bir dizi döndürür
2. `pinoox/pincore` `composer.json` içinde gerekli veya `vendor/pinoox/pincore` mevcut

Algılanan paketi ortam değişkenleriyle geçersiz kılın:

| Değişken | Amaç |
|----------|---------|
| `PINX_PACKAGE` | CLI hedef paketini zorla |
| `PINOOX_DEV_APP` | `PINX_PACKAGE` için takma ad |
| `PINX_DEV=1` | Dev modu (pincore'a delege ederken pinx tarafından otomatik ayarlanır) |

---

## Gereksinimler

- **PHP** ≥ 8.1, `pinoox/pincore` tarafından gerekli extension'larla
- **Composer** 2.x
- **Node.js** + npm — yalnızca Vite/Vue/React frontend'leri kullanıldığında
- **Veritabanı** — MySQL/MariaDB veya `.env`'inizin yapılandırdığı (statik/yalnızca Twig uygulamaları için isteğe bağlı)

---

## İlgili dokümantasyon

- [Pinoox kurulumu](./installing-pinoox.md)
- [Pinoox CLI referansı (çok uygulamalı)](./cli-reference.md)
- [İlk uygulamanız](./your-first-app.md)
- [app.php manifest](./app-manifest.md)

---

[← Dizine dön](../README.md)
