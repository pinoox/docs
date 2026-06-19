# Destek ile iletişim

[← Dizine dön](../README.md)

[Sık karşılaşılan sorunlar](./common-issues.md) bölümünü inceledikten sonra hâlâ bir engeliniz varsa aşağıdaki resmi kanalları kullanın. Destekle iletişime geçmeden önce Pinoox sürümünüzü, PHP sürümünüzü, hata mesajınızı ve yeniden üretme adımlarınızı hazırlayın.

---

## Genel destek

**E-posta:** [support@pinoox.com](mailto:support@pinoox.com)

Uygun olduğu durumlar:

- Kurulum ve dağıtım soruları
- Beklenmeyen framework davranışı
- HMVC ve uygulama mimarisi rehberliği

E-postanıza şunları ekleyin:

1. Pinoox sürümü (`composer.json` → `version` veya git etiketi)
2. PHP sürümü (`php -v`)
3. İşletim sistemi ve web sunucusu (Apache/nginx, MAMP, cPanel, …)
4. Tam hata metni veya ekran görüntüsü
5. Minimal yeniden üretme adımları

---

## GitHub Issues

Onaylanmış hatalar, özellik istekleri ve genel teknik tartışma için:

**Depo:** [github.com/pinoox/pinoox](https://github.com/pinoox/pinoox/issues)

Yeni issue açmadan önce:

- Yinelenen issue'ları arayın
- En son kararlı/beta sürümde test edin
- `pincore` ile ilgiliyse `pinoox/pincore` paketini de kontrol edin

Önerilen issue şablonu:

```markdown
## Environment
- Pinoox: 3.1-beta
- PHP: 8.2.x
- OS: Windows / Linux

## Expected
...

## Actual
...

## Steps to reproduce
1. ...
2. ...
```

---

## Güvenlik raporları

**E-posta:** [security@pinoox.com](mailto:security@pinoox.com)

**Yalnızca** güvenlik açıkları için — SQL injection, auth bypass, RCE, secret sızıntısı.

- Yama hazır olana kadar ayrıntıları kamuya açmayın (GitHub issue)
- Mümkün olduğunda minimal PoC ve etki açıklaması ekleyin

---

## Koda katkı

PR'ler ve framework geliştirmesi için:

- [Katkıda bulunma](../introduction/contributions.md)
- Fork → dal → test (`php pinoox test`) → Pull Request

---

## Kendi kendine yardım kaynakları

| Konu | Dokümantasyon |
|-------|-----|
| Kurulum | [installing-pinoox.md](../start/installing-pinoox.md) |
| İlk uygulama | [your-first-app.md](../start/your-first-app.md) |
| Sık sorunlar | [common-issues.md](./common-issues.md) |
| Test | [getting-started.md](../test/getting-started.md) |

**Web sitesi:** [pinoox.com](https://www.pinoox.com/)

---

## İlgili dokümantasyon

- [Sık karşılaşılan sorunlar](./common-issues.md)
- [Pinoox nedir?](../introduction/what-is-pinoox.md)
- [Katkıda bulunma](../introduction/contributions.md)
- [Pinoox kurulumu](../start/installing-pinoox.md)

---

[← Dizine dön](../README.md)
