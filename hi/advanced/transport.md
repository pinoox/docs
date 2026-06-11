# Transport (साझा संसाधन)

[← अनुक्रमणिका पर वापस जाएँ](../README.md)

HMVC आर्किटेक्चर में, ऐप्स `app.php` के **`transport`** ब्लॉक के माध्यम से users, auth, फ़ाइलें और अनुमतियाँ आपस में साझा कर सकते हैं। Transport के बिना, हर ऐप प्रत्येक संसाधन को अपने package के लिए **local** रखता है।

| शब्द | अर्थ |
|------|---------|
| **`platform`** | लॉजिकल साझा scope — साझा DB पंक्तियाँ `app = platform` का उपयोग करती हैं |
| **`pincore/`** | केवल भौतिक framework फ़ोल्डर — **कभी भी** transport scope मान नहीं |

---

## यह कैसे काम करता है

Transport की दो परतें हैं:

1. **Scenario** — एक-शब्द का preset जो कई granular keys में विस्तारित होता है।
2. **Granular key** — एक विशिष्ट साझा संसाधन के लिए बहु-शब्द नाम।

```php
// app.php
'transport' => [
    'full' => 'platform',           // scenario preset
    'file_storage' => 'local',      // granular override
],
```

**Resolution क्रम:** स्पष्ट granular key → मेल खाने वाला scenario।

Granular keys हमेशा scenario विस्तार पर भारी पड़ती हैं। यदि कोई key सेट नहीं है और कोई scenario उसे कवर नहीं करता, तो ऐप उस संसाधन को **local** (वर्तमान package) रखता है।

---

## Scope मान

प्रत्येक scenario या granular key को एक scope सौंपा जाता है:

| Scope | अर्थ |
|-------|---------|
| `local` | वर्तमान ऐप package (छोड़े जाने पर डिफ़ॉल्ट) |
| `platform` | साझा platform scope (`app = platform`, `pinx_*` tables) |
| `host` | वह ऐप जिसने इसे खोला (preview / `App::meeting()`) |
| `{package}` | स्पष्ट ऐप, जैसे `com_pinoox_manager` |

**`auth_config`** और **`auth_cookie`** के लिए, `platform` और `{package}` उस ऐप पर resolve होते हैं जो **auth सेटिंग्स प्रदान करता है** (इंस्टॉल होने पर आमतौर पर `com_pinoox_manager`)।

---

## Scenarios संदर्भ

एक-शब्द presets। `app.php` में `'transport' => ['{scenario}' => '{scope}']` के रूप में उपयोग करें।

| Scenario | विवरण | शामिल granular keys |
|----------|-------------|------------------------|
| `full` | सभी साझा संसाधन | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | लॉगिन सिस्टम: accounts, auth, session tokens | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | फ़ाइल अपलोड और मेटाडेटा | `file_storage` |
| `access` | Roles और अनुमतियाँ | `access_table` |

---

## Granular keys संदर्भ

बहु-शब्द संसाधन नाम। किसी एक संसाधन को साझा या override करने के लिए उपयोग करें।

| Granular key | नियंत्रित करता है | किसके द्वारा उपयोग |
|--------------|----------|---------|
| `user_table` | `UserModel` `app` कॉलम / global scope | User accounts |
| `auth_config` | Auth mode, JWT secret, lifetimes (`auth` ब्लॉक स्रोत) | `AuthConfig`, login flow |
| `auth_cookie` | Client key / cookie नाम (`auth.key`) | Cookie और SPA token storage |
| `session_token` | `TokenModel` `app` कॉलम / DB session पंक्तियाँ | Session persistence |
| `file_storage` | `FileModel` `app` कॉलम / upload पथ | Uploads और फ़ाइल मेटाडेटा |
| `access_table` | Role और permission model का `app` scope | `RoleModel`, `PermissionModel`, `can()` |

---

## सामान्य setups

**Platform के लिए auth provider (जैसे manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**Consumer ऐप — सब कुछ साझा, कोई local auth ब्लॉक नहीं:**

```php
'transport' => ['full' => 'platform'],
```

**केवल साझा लॉगिन:**

```php
'transport' => ['user' => 'platform'],
```

**Standalone ऐप** — `transport` छोड़ दें, या सब कुछ local पर pin करें:

```php
'transport' => ['user' => 'local'],
```

**Scenario के अंदर एक संसाधन को override करना:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## Code API

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // किसी granular key के लिए resolved package
Transport::authSource();                       // auth सेटिंग्स का मालिक ऐप, या null
Transport::sharesAuthWith($guest, $host);      // cross-app auth जाँच
Transport::resolved();                         // सभी granular keys → scope
Transport::activeScenarios();                  // जैसे ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Database

Platform-scoped tables connection **`platform`** और prefix **`pinx_`** का उपयोग करती हैं।

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## संबंधित दस्तावेज़

- [app.php manifest](../start/app-manifest.md)
- [User management](./user-management.md)
- [Access & permissions](./access-permissions.md)
- [File management](./file-management.md)

---

[← अनुक्रमणिका पर वापस जाएँ](../README.md)
