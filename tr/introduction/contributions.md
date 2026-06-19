# Pinoox'a katkıda bulunma

[← Dizine dön](../README.md)

Pinoox açık kaynaklı bir projedir. Hata raporlarından pull request'lere kadar katkılarınız framework'ü ve dokümantasyonunu geliştirmeye yardımcı olur.

---

## Katkı yolları

| Tür | Açıklama |
|------|-------------|
| Hata raporu | Yeniden üretme adımlarıyla GitHub Issue |
| Özellik isteği | Kullanım senaryosunu açıklayan Issue |
| Pull Request | Uygun depodaki hata düzeltmesi veya özellik |
| Dokümantasyon | `docs/` altındaki dosyaları iyileştirme (Farsça veya İngilizce) |
| Açık kaynak uygulama | Topluluk için bir Pinoox uygulaması yayınlama |

---

## Hata bildirme

Issue açarken şunları ekleyin:

1. **Başlık** — sorunun kısa özeti
2. **Yeniden üretme adımları** — adım adım
3. **Beklenen davranış** ve **gerçek davranış**
4. **Ortam** — PHP sürümü, Pinoox/pincore sürümü, işletim sistemi
5. **Örnek kod** — mümkün olduğunda

[Pinoox GitHub Issues](https://github.com/pinoox/pinoox/issues)

---

## Pull request'ler

### Depolar

- **pinoox/pinoox** — örnek proje, sistem uygulamaları, launcher
- **pinoox/pincore** — framework çekirdeği (`vendor/pinoox/pincore/`)

Çekirdek değişikliklerini yalnızca projenizdeki yerel `vendor/` kopyasına değil, pincore'a gönderin.

### Dal stratejisi (3.x)

- **Hata düzeltmeleri** → mevcut kararlı dal (ör. `3.x`)
- **Küçük, uyumlu özellikler** → aynı kararlı dal
- **Kırıcı veya büyük değişiklikler** → `master` / sonraki sürüm dalı

### Kod standartları

- Kod stili için [PSR-12](https://www.php-fig.org/psr/psr-12/)
- Autoloading için [PSR-4](https://www.php-fig.org/psr/psr-4/)
- PHP 8.2+
- Açık, emir kipi commit mesajları (ör. `Fix route validation for missing actions`)

---

## Güvenlik

Güvenlik açıklarını **gizli** olarak bildirin:

`security@pinoox.com`

---

## İletişim

- Destek: `support@pinoox.com`
- [GitHub deposu](https://github.com/pinoox/pinoox)

---

## İlgili dokümantasyon

- [Pinoox nedir?](./what-is-pinoox.md)
- [Pinoox özellikleri](./features-pinoox.md)

---

[← Dizine dön](../README.md)
