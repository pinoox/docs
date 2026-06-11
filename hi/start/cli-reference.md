# Pinoox CLI संदर्भ

[← इंडेक्स पर वापस जाएँ](../README.md)

हर कमांड **प्रोजेक्ट रूट** से चलाएँ:

```bash
php pinoox
php pinoox list
php pinoox help migrate
```

जब किसी package की आवश्यकता हो और वह न दिया गया हो, तो Pinoox एक इंटरैक्टिव पिकर दिखाता है।

> **सिंगल-ऐप** प्रोजेक्ट्स के लिए, स्टैंडअलोन [Pinx CLI](./pinx-cli.md) का उपयोग करें (`pinx dev`, `pinx setup`, `pinx build`, …)।

---

## सामान्य उपनाम (aliases)

| Alias | कमांड |
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

## ऐप्स

| कमांड | उद्देश्य |
|---------|---------|
| `app:create {package}` | ऐप स्कैफ़ोल्ड करें (`--simple`, `--stack`, `--profile`) |
| `app:list` | ऐप्स की सूची |
| `app:delete` | ऐप हटाएँ |
| `app:router set /path {package}` | URL मैपिंग |
| `app:domain` | होस्ट → ऐप मैप |
| `app:resolve` | सक्रिय ऐप को डीबग करें |

---

## स्कैफ़ोल्डिंग

| कमांड | आउटपुट |
|---------|--------|
| `controller:create` | `Controller/` |
| `model:create` | `Model/` |
| `portal:create` | `Portal/` |
| `form-request:create` | FormRequest क्लास |
| `seeder:create` | `database/seed/` |
| `test:create` | Pest फ़ाइल |
| `theme:frontend` | फ्रंटएंड टूलिंग (Vue/React/Twig) |

---

## डेटाबेस

| कमांड | उद्देश्य |
|---------|---------|
| `migrate {package}` | Migrations चलाएँ (app, `platform`, `pincore`) |
| `migrate:create` | नई migration फ़ाइल |
| `migrate:status` / `migrate:rollback` | स्थिति / रोलबैक |
| `seeder:run` | Seeders चलाएँ |
| `patch:create` / `patch:run` / `patch:status` / `patch:rollback` | [Patches](../database/patches.md) |
| `query` | Raw SQL (डीबग) |

---

## Cache और Pinker

| कमांड | उद्देश्य |
|---------|---------|
| `cache:build` / `cache:clear` | रनटाइम cache |
| `pinker:status` / `pinker:rebuild` / `pinker:diff` / `pinker:clear` | [Pinker](../advanced/pinker.md) |
| `reset` | Pinker + config रीसेट करें |

---

## Schedule

| कमांड | उद्देश्य |
|---------|---------|
| `schedule:list` | Cron कार्यों की सूची |
| `schedule:run` | नियत (due) कार्य चलाएँ |

देखें [Schedule](../advanced/schedule.md)।

---

## Router

| कमांड | उद्देश्य |
|---------|---------|
| `route:actions {package}` | Named Actions की सूची |

---

## Pinx पैकेजिंग

| कमांड | उद्देश्य |
|---------|---------|
| `pinx:build` | `.pinx` पैकेज बनाएँ |
| `pinx:install` | पैकेज इंस्टॉल करें |
| `pinx:info` | मेटाडेटा |
| `wizard:list` / `wizard:install` | इंस्टॉल विज़ार्ड |

---

## डेवलपमेंट

| कमांड | उद्देश्य |
|---------|---------|
| `test` | Pest टेस्ट |
| `serve` | बिल्ट-इन डेव सर्वर |
| `log:view` / `log:clear` | लॉग |
| `deps` | सभी ऐप्स में Composer/npm |
| `version` / `mode:show` | संस्करण / रनटाइम मोड |

---

## Package आर्ग्युमेंट

| मान | अर्थ |
|-------|---------|
| `com_my_shop` | विशिष्ट ऐप |
| `platform` | प्लेटफ़ॉर्म migrations/patches/seeders |
| `pincore` | फ्रेमवर्क कोर |
| `all` | सभी ऐप्स (cache/pinker) |

---

## संबंधित दस्तावेज़

- [आपका पहला ऐप](./your-first-app.md)
- [Migrations](../database/migrations.md)
- [Patches](../database/patches.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
