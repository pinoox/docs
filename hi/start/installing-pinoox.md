# Pinoox इंस्टॉल करना

[← इंडेक्स पर वापस जाएँ](../README.md)

यह गाइड Pinoox 3.x को इंस्टॉल करने के बारे में है। शुरू करने के दो तरीके हैं:

| तरीका | किसके लिए सबसे अच्छा |
|-------|----------|
| **A. [Pinx CLI](./pinx-cli.md) के साथ सिंगल-ऐप** | एक ऐप बनाना — सबसे तेज़ शुरुआत, कोई मैनेजर UI नहीं |
| **B. पूर्ण प्लेटफ़ॉर्म (क्लासिक)** | ग्राफ़िकल इंस्टॉलर और मैनेजर के साथ कई ऐप्स होस्ट करना |

---

## आवश्यकताएँ

| टूल | संस्करण |
|------|---------|
| PHP | 8.2 या उससे ऊपर (ext-mysqli, ext-zip के साथ) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (वैकल्पिक) | 18+ — केवल फ्रंटएंड थीम बिल्ड के लिए |

---

## तरीका A — Pinx CLI के साथ सिंगल-ऐप

[Pinx CLI](./pinx-cli.md) को एक बार इंस्टॉल करें, नया ऐप बनाएँ, और उसे चलाएँ:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # com_my_shop सुझाता है — विज़ार्ड में पुष्टि करें या संपादित करें
cd my-shop
cp .env.example .env          # यदि आप डेटाबेस उपयोग करते हैं तो DB_* सेट करें
pinx setup                    # platform + app को migrate करें, seeders चलाएँ
pinx dev                      # http://127.0.0.1:8000
```

या ग्लोबल इंस्टॉल के बिना, प्रोजेक्ट टेम्पलेट के माध्यम से:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

PHP, env, DB और बिल्ड तैयारी जाँचने के लिए किसी भी समय `pinx doctor` चलाएँ। दैनिक वर्कफ़्लो और कमांड संदर्भ के लिए पूरी [Pinx CLI गाइड](./pinx-cli.md) देखें।

---

## तरीका B — पूर्ण प्लेटफ़ॉर्म (क्लासिक)

### 1. प्रोजेक्ट प्राप्त करें

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

वैकल्पिक रूप से, [GitHub](https://github.com/pinoox/pinoox) से नवीनतम रिलीज़ डाउनलोड करें, उसे एक्सट्रैक्ट करें, फिर `composer install` चलाएँ।

---

### 2. इसे अपने वेब सर्वर में रखें

प्रोजेक्ट फ़ोल्डर को अपने document root में रखें:

| एनवायरनमेंट | उदाहरण पाथ |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Document root को **प्रोजेक्ट रूट** (वह फ़ोल्डर जिसमें `index.php` है) पर सेट करें — किसी `public` सबफ़ोल्डर पर नहीं।

---

### 3. डेटाबेस बनाएँ

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. इंस्टॉलर चलाएँ

अपना ब्राउज़र खोलें:

```
http://localhost/pinoox
```

सिस्टम ऐप `com_pinoox_installer` चलता है। GUI के चरण हैं:

1. PHP आवश्यकताओं की जाँच करें
2. लाइसेंस अनुबंध स्वीकार करें
3. डेटाबेस क्रेडेंशियल दर्ज करें
4. एडमिन खाता बनाएँ
5. इंस्टॉलेशन पूरा करें

---

### 5. इंस्टॉलेशन के बाद

मुख्य लेआउट:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← ऐप्स
├── vendor/pinoox/pincore/  ← कोर
└── config/             ← प्रोजेक्ट config
```

अपना पहला ऐप बनाएँ:

```bash
php pinoox app:create com_acme_blog
```

---

## त्वरित समस्या निवारण

| समस्या | समाधान |
|---------|-----|
| खाली पेज | `composer install` चलाएँ और PHP error logs जाँचें |
| सब-routes पर 404 | mod_rewrite / `.htaccess` सक्षम करें |
| Missing extension त्रुटि | php.ini में ext-mysqli और ext-zip सक्षम करें |
| इंस्टॉलर नहीं खुलता | Document root और रनटाइम फ़ोल्डरों पर write अनुमतियाँ सत्यापित करें |

---

## संबंधित दस्तावेज़

- [Pinx CLI (सिंगल-ऐप)](./pinx-cli.md)
- [आपका पहला ऐप](./your-first-app.md)
- [प्रोजेक्ट संरचना](./structure.md)
- [Pinoox क्या है?](../introduction/what-is-pinoox.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
