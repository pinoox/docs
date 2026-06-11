# Pinoox CLI referansı

[← Dizine dön](../README.md)

Her komutu **proje kökünden** çalıştırın:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

Paket gerekli olduğunda ve belirtilmediğinde Pinoox etkileşimli bir seçici gösterir.

> **Tek uygulamalı** projeler için bağımsız [Pinx CLI](./pinx-cli.md) kullanın (`pinx dev`, `pinx setup`, `pinx build`, …).

---

## Yaygın takma adlar

| Takma ad | Komut |
|-------|---------|
| `mg` | `migrate` |
| `mg:create` | `migrate:create` |
| `patch` | `patch:run` |
| `seed` | `seeder:run` |
| `cb` | `cache:build` |
| `cc` | `cache:clear` |
| `bake` | `pinker:rebuild` |
| `apps` | `app:list` |
| `make:app` | `app:create` |
| `router` | `app:router` |
| `routes` | `route:actions` |

---

## Uygulamalar

| Komut | Amaç |
|---------|---------|
| `app:create {package}` | Uygulama iskeleti (`--simple`, `--stack`, `--profile`) |
| `app:list` | Uygulamaları listele |
| `app:delete` | Uygulamayı kaldır |
| `app:router set /path {package}` | URL eşlemesi |
| `app:domain` | Host → uygulama haritası |
| `app:resolve` | Aktif uygulamayı debug et |

---

## İskelet oluşturma

| Komut | Çıktı |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest sınıfı |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest dosyası |
| `theme:frontend` | Frontend araçları (Vue/React/Twig) |

---

## Veritabanı

| Komut | Amaç |
|---------|---------|
| `migrate {package}` | Migration'ları çalıştır (uygulama, `platform`, `pincore`) |
| `migrate:create` | Yeni migration dosyası |
| `migrate:status` / `migrate:rollback` | Durum / geri alma |
| `seeder:run` | Seeder'ları çalıştır |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patch'ler](../database/patches.md) |
| `query` | Ham SQL (debug) |

---

## Önbellek ve Pinker

| Komut | Amaç |
|---------|---------|
| `cache:build` / `cache:clear` | Runtime önbelleği |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + config sıfırla |

---

## Zamanlama

| Komut | Amaç |
|---------|---------|
| `schedule:list` | Cron görevlerini listele |
| `schedule:run` | Vadesi gelen görevleri çalıştır |

Bkz. [Zamanlama](../advanced/schedule.md).

---

## Router

| Komut | Amaç |
|---------|---------|
| `route:actions {package}` | Named Action'ları listele |

---

## Pinx paketleme

| Komut | Amaç |
|---------|---------|
| `pinx:build` | `.pinx` paketi oluştur |
| `pinx:install` | Paket kur |
| `pinx:info` | Meta veri |
| `wizard:list` / `wizard:install` | Kurulum sihirbazı |

---

## Geliştirme

| Komut | Amaç |
|---------|---------|
| `test` | Pest testleri |
| `serve` | Yerleşik dev sunucusu |
| `log:view` / `log:clear` | Loglar |
| `deps` | Uygulamalar genelinde Composer/npm |
| `version` / `mode:show` | Sürüm / runtime modu |

---

## Paket argümanı

| Değer | Anlam |
|-------|---------|
| `com_my_shop` | Belirli uygulama |
| `platform` | Platform migration'ları/patch'leri/seeder'ları |
| `pincore` | Framework çekirdeği |
| `all` | Tüm uygulamalar (önbellek/pinker) |

---

## İlgili dokümantasyon

- [İlk uygulamanız](./your-first-app.md)
- [Migration'lar](../database/migrations.md)
- [Patch'ler](../database/patches.md)

---

[← Dizine dön](../README.md)
