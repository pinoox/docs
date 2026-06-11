# Pinx CLI (सिंगल-ऐप प्रोजेक्ट्स)

[← इंडेक्स पर वापस जाएँ](../README.md)

**[Pinx CLI](https://github.com/pinoox/pinx-cli)** **सिंगल-ऐप** Pinoox प्रोजेक्ट्स के लिए डेवलपर CLI है — मल्टी-ऐप मैनेजर को छुए बिना स्कैफ़ोल्ड करें, चलाएँ, migrate करें, बिल्ड करें और `.pinx` पैकेज शिप करें।

यह `pinoox/pincore` और `pinoox/app` टेम्पलेट पर बना है। आपका प्रोजेक्ट रूट **ही** ऐप है: एक `app.php`, एक package, एक वर्कफ़्लो।

> क्लासिक मल्टी-ऐप प्लेटफ़ॉर्म इंस्टॉल के लिए, इसके बजाय [`php pinoox`](./cli-reference.md) का उपयोग करें।

---

## त्वरित शुरुआत

Pinx को एक बार इंस्टॉल करें, नया ऐप बनाएँ, और उसे चलाएँ:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # com_my_shop सुझाता है — विज़ार्ड में पुष्टि करें या संपादित करें
cd my-shop
cp .env.example .env          # यदि आप डेटाबेस उपयोग करते हैं तो DB_* सेट करें
pinx setup                    # platform + app को migrate करें, seeders चलाएँ
pinx dev                      # http://127.0.0.1:8000
```

यदि `pinx` नहीं मिलता तो Composer का ग्लोबल `bin` अपने `PATH` में जोड़ें:

- Linux / macOS: `~/.composer/vendor/bin` या `~/.config/composer/vendor/bin`
- Windows: `%APPDATA%\Composer\vendor\bin`

| चरण | यह क्या करता है |
|------|--------------|
| `composer global require` | आपकी मशीन पर `pinx` कमांड इंस्टॉल करता है |
| `pinx new my-shop` | `pinoox/app` से स्कैफ़ोल्ड करता है; विज़ार्ड 3-भाग वाला package सुझाता है (जैसे `com_my_shop`) |
| `.env` | डेटाबेस और प्रोजेक्ट पाथ — `.env.example` से कॉपी करें |
| `pinx setup` | एक ही बार में: प्लेटफ़ॉर्म migrations → ऐप migrations → seeders |
| `pinx dev` | PHP डेव सर्वर; फ्रंटएंड स्टैक कॉन्फ़िगर होने पर Vite भी शुरू करता है |

Package के नाम `com_{vendor}_{name}` प्रारूप का पालन करते हैं — जैसे `com_acme_shop`, `ir_yekdo_app`। पहले से किसी खाली फ़ोल्डर के अंदर हैं? `pinx new` के बजाय `pinx init` का उपयोग करें।

**`setup` से पहले वैकल्पिक जाँच:** `pinx doctor` PHP, लेआउट, env, DB और बिल्ड तैयारी की रिपोर्ट देता है।

---

## विकल्प: `composer create-project`

ग्लोबल इंस्टॉल नहीं चाहिए — टेम्पलेट प्रोजेक्ट के अंदर `bin/pinx` के साथ आता है:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

---

## सिंगल-ऐप को क्या अलग बनाता है

क्लासिक Pinoox इंस्टॉल कई ऐप्स को `apps/` के अंतर्गत रखते हैं और रनटाइम पर एक चुनते हैं। **सिंगल-ऐप** इसे सरल (flatten) कर देता है:

- प्रोजेक्ट रूट पर `app.php` package की पहचान और pinx सेटिंग्स रखता है
- `Controller/`, `Model/`, `routes/`, `theme/` रूट पर रहते हैं — `apps/{package}/` के अंदर नहीं
- `platform/` लोकल routing और लॉन्चर config रखता है (`.pinx` बिल्ड से बाहर रखा जाता है)
- Pinx हमेशा **आपके** ऐप को लक्षित करता है — न कोई package पिकर, न कोई मैनेजर UI

```
my-shop/                    ← प्रोजेक्ट रूट = ऐप रूट
├── app.php                 ← package, version, pinx.sign, frontend.stack
├── Controller/ Model/ routes/ theme/
├── platform/               ← डेव होस्ट + डिप्लॉय परत (केवल लोकल)
├── bin/pinx                ← प्रोजेक्ट-लोकल CLI एंट्री
└── vendor/pinoox/pincore   ← फ्रेमवर्क
```

---

## इंस्टॉलेशन विकल्प

| कहाँ | कैसे | कब उपयोग करें |
|-------|-----|-------------|
| **ग्लोबल** | `composer global require pinoox/pinx-cli` | अनुशंसित — कहीं से भी `pinx new` और `pinx init` |
| **प्रति प्रोजेक्ट** | `pinoox/app` में `bin/pinx` के रूप में शामिल | `composer create-project` के बाद — ग्लोबल इंस्टॉल की आवश्यकता नहीं |

```bash
pinx -v          # CLI संस्करण (जैसे pinx-cli 1.1.7)
pinx list        # समूहित कमांड अवलोकन
pinx help setup  # एक कमांड का विवरण
```

---

## दैनिक वर्कफ़्लो

```bash
pinx dev                    # लोकल सर्वर (+ Vite जब app.php → frontend.stack सेट हो)
pinx dev --open             # शुरू होने के बाद ब्राउज़र खोलें
pinx dev --no-frontend      # केवल PHP

pinx migrate                # ऐप migrations चलाएँ (--platform पहले प्लेटफ़ॉर्म चलाता है)
pinx migrate:st             # migration स्थिति
pinx migrate:cr create_products_table

pinx make controller ProductController
pinx make model ProductModel
pinx make migration create_products_table
pinx make portal ShopService

pinx routes                 # named actions की सूची (--validate, --json)
pinx test                   # ऐप टेस्ट चलाएँ (Pest)
```

**फ्रंटएंड** (जब `theme/` Vue/React + Vite का उपयोग करे):

```bash
pinx fe:info                # स्टैक, npm स्क्रिप्ट्स, पाथ
pinx fe:i                   # npm install
pinx fe:d                   # Vite डेव सर्वर
pinx fe:b                   # प्रोडक्शन बिल्ड
pinx fe:sc --stack=vue      # स्टार्टर फ़ाइलें स्कैफ़ोल्ड करें
```

**डिपेंडेंसीज़:**

```bash
pinx deps:st                # Composer + npm स्थिति
pinx deps:i                 # सभी इंस्टॉल करें
pinx deps:up                # सभी अपडेट करें
```

**Pinker** (बिल्ड cache):

```bash
pinx pinker:st              # cache बनाम source
pinx pinker:rb              # पुनर्निर्माण (rebuild)
pinx pinker:df              # अंतर (diff)
```

---

## प्रोडक्शन में शिप करें

पूर्ण Pinoox प्लेटफ़ॉर्म पर इंस्टॉलेशन के लिए एक `.pinx` पैकेज बनाएँ (Manager → Applications):

```bash
pinx build                  # → export/*.pinx
pinx build -o /tmp/shop.pinx
pinx release --bump=patch   # app.php में संस्करण bump करें + बिल्ड करें
pinx release --sign         # app.php → pinx.sign में key कॉन्फ़िगर होने पर साइन करें
```

`pinx build` समझदार डिफ़ॉल्ट लागू करता है (`vendor/`, `bin/`, `.env`, `platform/`, डेव टूलिंग को बाहर रखता है)। केवल आवश्यकता होने पर `app.php` में ओवरराइड करें:

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

Doctor एक संरचित डायग्नोस्टिक चलाता है और कुछ विफल होने पर समाधान कमांड सुझाता है:

| समूह | जाँचें |
|-------|--------|
| **Project** | `app.php`, package पहचान, `platform/` लेआउट |
| **Runtime** | PHP संस्करण (≥ 8.1), एक्सटेंशन, writable पाथ |
| **Dependencies** | Composer vendor, वैकल्पिक Node/npm |
| **Environment** | `.env` की मौजूदगी और मुख्य वेरिएबल |
| **Database** | कनेक्शन (`--skip-db` से छोड़ा जा सकता है) |
| **Frontend** | थीम स्टैक, `package.json` (`--skip-frontend` से छोड़ा जा सकता है) |
| **Build** | एक्सपोर्ट तैयारी, आइकन, संस्करण फ़ील्ड |

```bash
pinx doctor
pinx doctor --skip-db
pinx doctor --json          # CI-अनुकूल रिपोर्ट
pinx doctor --no-fixes      # सुझाए गए कमांड छिपाएँ
```

---

## कमांड संदर्भ

खंडवार अवलोकन के लिए `pinx list` चलाएँ। संक्षिप्त उपनाम (aliases) ब्रैकेट में दिखते हैं।

### Project

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `new` | — | `pinoox/app` से स्कैफ़ोल्ड करें (विज़ार्ड या flags) |
| `init` | — | वर्तमान डायरेक्टरी को इनिशियलाइज़ करें (ओवरराइट के लिए `--force`) |
| `setup` | — | DB: platform + app को migrate करें, फिर seed करें |
| `doctor` | `dr` | हेल्थ चेक — `--json`, `--skip-db`, `--skip-frontend` |
| `info` | `inf` | `app.php` से मेटाडेटा दिखाएँ |

### डेवलपमेंट

| कमांड | विवरण |
|---------|-------------|
| `dev` | डेव सर्वर; `frontend.stack` के vue/react होने पर Vite |

### डेटाबेस

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `migrate:run` | `migrate` | ऐप migrations चलाएँ (`--platform` पहले प्लेटफ़ॉर्म चलाता है) |
| `migrate:status` | `migrate:st` | Migration स्थिति |
| `migrate:rollback` | `migrate:rb` | अंतिम बैच रोलबैक करें (`--ignore-fk`) |
| `migrate:create <name>` | `migrate:cr` | Migration फ़ाइल बनाएँ |
| `migrate:platform` | `migrate:pl` | केवल प्लेटफ़ॉर्म migrations |
| `seeder:run` | `seed` | Seeders चलाएँ (`-c` class) |

### Patches

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `patch:run` | `patch` | लंबित patches चलाएँ |
| `patch:status` | `patch:st` | Patch स्थिति |
| `patch:rollback` | `patch:rb` | अंतिम patch बैच रोलबैक करें |

### बिल्ड और रिलीज़

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `build` | `bld` | `.pinx` पैकेज बनाएँ |
| `release` | `rel` | संस्करण bump + बिल्ड (`--bump`, `--sign`) |

### स्कैफ़ोल्डिंग

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `make <type> <name>` | `mk` | controller, model, migration, patch, portal, form-request, seeder, test |

### Routes

| कमांड | विवरण |
|---------|-------------|
| `route:actions` / `routes` | Named actions की सूची (`--validate`, `--json`) |

### डिपेंडेंसीज़

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `deps:status` | `deps:st` | Composer + npm स्थिति |
| `deps:install` | `deps:i` | डिपेंडेंसीज़ इंस्टॉल करें |
| `deps:update` | `deps:up` | डिपेंडेंसीज़ अपडेट करें |

### फ्रंटएंड

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `fe:info` | `fe:inf` | थीम स्टैक और npm स्क्रिप्ट्स |
| `fe:install` | `fe:i` | npm install |
| `fe:build` | `fe:b` | प्रोडक्शन बिल्ड |
| `fe:dev` | `fe:d` | Vite डेव सर्वर |
| `fe:scaffold` | `fe:sc` | स्टार्टर फ़ाइलें (`--stack=vue\|react\|twig`) |

### Schedule

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `schedule:list` | `sched:ls` | `schedule.php` से cron कार्यों की सूची |
| `schedule:run` | `sched:run` | नियत (due) कार्य चलाएँ (`--dry-run`) |

### Pinker

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `pinker:status` | `pinker:st` | Cache बनाम source |
| `pinker:rebuild` | `pinker:rb` | Cache का पुनर्निर्माण |
| `pinker:diff` | `pinker:df` | अंतर दिखाएँ |
| `pinker:clear` | `pinker:cl` | Cache साफ़ करें |
| `pinker:overrides` | `pinker:ov` | Overrides की सूची |

### गुणवत्ता और दस्तावेज़

| कमांड | विवरण |
|---------|-------------|
| `test` / `pest` | ऐप टेस्ट चलाएँ (`--unit`, `--feature`) |
| `api:docs` | REST API दस्तावेज़ीकरण |
| `graphql:docs` | GraphQL स्कीमा दस्तावेज़ीकरण |

### मेटा

| कमांड | Aliases | विवरण |
|---------|---------|-------------|
| `list` | — | समूहित कमांड अवलोकन |
| `version` | `ver` | CLI संस्करण |

---

## ऐप का पता लगाना (App detection)

Pinx वर्तमान कार्यशील डायरेक्टरी से ऊपर की ओर चलता है जब तक उसे एक वैध सिंगल-ऐप प्रोजेक्ट नहीं मिल जाता:

1. `app.php` मौजूद हो और एक array लौटाए जिसमें खाली न होने वाली `package` key हो
2. `composer.json` में `pinoox/pincore` आवश्यक हो, या `vendor/pinoox/pincore` मौजूद हो

एनवायरनमेंट वेरिएबल्स से पहचाने गए package को ओवरराइड करें:

| वेरिएबल | उद्देश्य |
|----------|---------|
| `PINX_PACKAGE` | CLI लक्ष्य package को बाध्य (force) करें |
| `PINOOX_DEV_APP` | `PINX_PACKAGE` का उपनाम |
| `PINX_DEV=1` | डेव मोड (pincore को सौंपते समय pinx द्वारा स्वतः सेट) |

---

## आवश्यकताएँ

- **PHP** ≥ 8.1, `pinoox/pincore` द्वारा आवश्यक एक्सटेंशनों के साथ
- **Composer** 2.x
- **Node.js** + npm — केवल Vite/Vue/React फ्रंटएंड उपयोग करते समय
- **डेटाबेस** — MySQL/MariaDB या जो भी आपका `.env` कॉन्फ़िगर करे (स्टैटिक/केवल-Twig ऐप्स के लिए वैकल्पिक)

---

## संबंधित दस्तावेज़

- [Pinoox इंस्टॉल करना](./installing-pinoox.md)
- [Pinoox CLI संदर्भ (मल्टी-ऐप)](./cli-reference.md)
- [आपका पहला ऐप](./your-first-app.md)
- [app.php मैनिफ़ेस्ट](./app-manifest.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
