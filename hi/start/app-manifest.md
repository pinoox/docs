# app.php मैनिफ़ेस्ट संदर्भ

[← इंडेक्स पर वापस जाएँ](../README.md)

`app.php` आपके ऐप का मैनिफ़ेस्ट है। डिफ़ॉल्ट मान `vendor/pinoox/pincore/Component/Package/data/source.php` में रहते हैं — केवल वही ओवरराइड करें जिसकी आपको आवश्यकता हो।

---

## पहचान और सक्रियण

| Key | उद्देश्य |
|-----|---------|
| `package` | फ़ोल्डर नाम = namespace (`com_acme_shop`) |
| `name` | प्रदर्शित नाम |
| `enable` | ऐप सक्षम / अक्षम करें |
| `description`, `developer`, `icon` | मेटाडेटा |
| `version-name`, `version-code` | ऐप संस्करण |
| `sys-app`, `hidden`, `dock` | सिस्टम ऐप / छिपा हुआ / मैनेजर डॉक |
| `minpin` | न्यूनतम प्लेटफ़ॉर्म संस्करण |

---

## Router और boot

| Key | उद्देश्य |
|-----|---------|
| `router.routes` | `routes/*.php` फ़ाइलें |
| `boot` | `boot.php` चलाएँ (डिफ़ॉल्ट true) |
| `boot-global` | हर HTTP request पर boot करें |
| `extends` | होस्ट ऐप के boot होने पर boot करें |
| `loader` | अतिरिक्त फ़ाइलें (`func.php`) |
| `depends` | आवश्यक ऐप्स |

देखें [boot.php और events](../advanced/boot-and-events.md)।

---

## Flow और सुरक्षा

| Key | उद्देश्य |
|-----|---------|
| `flow` | ग्लोबल flows (BootFlow) |
| `alias` | नाम → Flow क्लास |
| `auth` | mode, lifetime, JWT/cookie |
| `access` | RBAC: `groups`, `super_roles` |
| `transport` | उपयोगकर्ता/फ़ाइल/एक्सेस को प्लेटफ़ॉर्म के साथ साझा करें |

देखें [Flows](../basic/flows.md), [उपयोगकर्ता प्रबंधन](../advanced/user-management.md), [एक्सेस](../advanced/access-permissions.md)।

---

## UI और थीम

| Key | उद्देश्य |
|-----|---------|
| `theme` | सक्रिय थीम फ़ोल्डर |
| `theme-context`, `theme-contexts`, `theme-extends` | मल्टी-कॉन्टेक्स्ट / इनहेरिटेंस |
| `frontend` | `stack`, `profile`, `entry`, `manifest` |
| `lang` | डिफ़ॉल्ट locale |
| `open` | मैनेजर में खोलने का व्यवहार |

---

## डेटाबेस और स्टोरेज

| Key | उद्देश्य |
|-----|---------|
| `database` | DB कनेक्शन ओवरराइड |
| `table.prefix` | टेबल प्रीफ़िक्स |
| `transport.user` / `file_storage` / `access` | प्रीसेट या विस्तृत (granular) keys |
| `filesystem` | disk, hash_length, dispatcher, file_policy, groups, thumbs |

---

## रनटाइम

| Key | उद्देश्य |
|-----|---------|
| `runtime.mode`, `runtime.debug` | मोड ओवरराइड |
| `cache` | routes/api/boot/twig को bake करें |
| `log`, `redis`, `date` | प्रति-ऐप ओवरराइड |
| `container` | DI bindings |

---

## Pinker / Pinx

| Key | उद्देश्य |
|-----|---------|
| `pinx` | type, minpin, sign |
| `build` | पैकेजों के लिए exclude/include |

---

## संयुक्त उदाहरण

```php
<?php

return [
    'package' => 'com_acme_portal',
    'name' => 'Portal',
    'enable' => true,
    'theme' => 'default',
    'transport' => ['user' => 'platform'],
    'auth' => ['mode' => 'cookie', 'lifetime' => 30, 'lifetime_unit' => 'day'],
    'access' => [
        'enabled' => true,
        'super_roles' => ['admin'],
        'groups' => ['editor' => ['blog.*']],
    ],
    'flow' => [App\com_acme_portal\Flow\BootFlow::class],
    'alias' => ['auth' => App\com_acme_portal\Flow\AuthFlow::class],
    'router' => ['routes' => ['routes/web.php', 'routes/actions.php', 'routes/api.php']],
    'frontend' => ['stack' => 'twig', 'profile' => 'hybrid'],
];
```

---

## संबंधित दस्तावेज़

- [प्रोजेक्ट संरचना](./structure.md)
- [Config](../basic/config.md)

---

[← इंडेक्स पर वापस जाएँ](../README.md)
