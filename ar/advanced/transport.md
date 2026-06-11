# النقل (Transport) — الموارد المشتركة

[← العودة إلى الفهرس](../README.md)

في معمارية HMVC، يمكن للتطبيقات مشاركة المستخدمين والمصادقة (Auth) والملفات والصلاحيات فيما بينها من خلال كتلة **`transport`** في `app.php`. بدون transport، يبقي كل تطبيق جميع موارده **محلية (local)** ضمن حزمته الخاصة.

| المصطلح | المعنى |
|------|---------|
| **`platform`** | نطاق مشترك منطقي — تستخدم صفوف قاعدة البيانات المشتركة `app = platform` |
| **`pincore/`** | مجلد إطار العمل المادي فقط — **لا يكون أبداً** قيمة لنطاق transport |

---

## كيف يعمل

يتكوّن النقل (Transport) من طبقتين:

1. **السيناريو (Scenario)** — إعداد مسبق من كلمة واحدة يتوسّع إلى عدة مفاتيح تفصيلية.
2. **المفتاح التفصيلي (Granular key)** — اسم من عدة كلمات لمورد مشترك واحد محدّد.

```php
// app.php
'transport' => [
    'full' => 'platform',           // إعداد مسبق (سيناريو)
    'file_storage' => 'local',      // تجاوز تفصيلي
],
```

**ترتيب الحل:** المفتاح التفصيلي الصريح ← ثم السيناريو المطابق.

تتفوق المفاتيح التفصيلية دائماً على توسّع السيناريو. وإذا لم يُعيَّن مفتاح ولم يغطّه أي سيناريو، يبقي التطبيق ذلك المورد **محلياً** (الحزمة الحالية).

---

## قيم النطاق (Scope)

يُسنَد لكل سيناريو أو مفتاح تفصيلي نطاق واحد:

| النطاق | المعنى |
|-------|---------|
| `local` | حزمة التطبيق الحالي (الافتراضي عند الإغفال) |
| `platform` | نطاق المنصة المشترك (`app = platform`، جداول `pinx_*`) |
| `host` | التطبيق الذي فتح هذا التطبيق (المعاينة / `App::meeting()`) |
| `{package}` | تطبيق محدّد صراحةً، مثل `com_pinoox_manager` |

بالنسبة لـ **`auth_config`** و **`auth_cookie`**، تُحَل القيمتان `platform` و `{package}` إلى التطبيق الذي **يوفّر إعدادات المصادقة** (عادةً `com_pinoox_manager` عند تثبيته).

---

## مرجع السيناريوهات

إعدادات مسبقة من كلمة واحدة. تُستخدم في `app.php` بالشكل `'transport' => ['{scenario}' => '{scope}']`.

| السيناريو | الوصف | المفاتيح التفصيلية المشمولة |
|----------|-------------|------------------------|
| `full` | جميع الموارد المشتركة | `user_table`، `auth_config`، `auth_cookie`، `session_token`، `file_storage`، `access_table` |
| `user` | نظام تسجيل الدخول: الحسابات، المصادقة، رموز الجلسات | `user_table`، `auth_config`، `auth_cookie`، `session_token` |
| `storage` | الملفات المرفوعة وبياناتها الوصفية | `file_storage` |
| `access` | الأدوار والصلاحيات | `access_table` |

---

## مرجع المفاتيح التفصيلية

أسماء موارد من عدة كلمات. تُستخدم لمشاركة مورد واحد أو تجاوزه.

| المفتاح التفصيلي | يتحكم في | يُستخدم بواسطة |
|--------------|----------|---------|
| `user_table` | عمود `app` في `UserModel` / النطاق العام | حسابات المستخدمين |
| `auth_config` | نمط المصادقة، سر JWT، مدد الصلاحية (مصدر كتلة `auth`) | `AuthConfig`، تدفق تسجيل الدخول |
| `auth_cookie` | مفتاح العميل / اسم الكوكيز (`auth.key`) | تخزين الكوكيز ورموز SPA |
| `session_token` | عمود `app` في `TokenModel` / صفوف الجلسات في قاعدة البيانات | استمرارية الجلسات |
| `file_storage` | عمود `app` في `FileModel` / مسارات الرفع | الرفع والبيانات الوصفية للملفات |
| `access_table` | نطاق `app` لنماذج الأدوار والصلاحيات | `RoleModel`، `PermissionModel`، `can()` |

---

## إعدادات شائعة

**موفّر مصادقة للمنصة (مثل manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**تطبيق مستهلك — مشاركة كل شيء، بدون كتلة auth محلية:**

```php
'transport' => ['full' => 'platform'],
```

**مشاركة تسجيل الدخول فقط:**

```php
'transport' => ['user' => 'platform'],
```

**تطبيق مستقل** — احذف `transport`، أو ثبّت كل شيء محلياً:

```php
'transport' => ['user' => 'local'],
```

**تجاوز مورد واحد ضمن سيناريو:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## واجهة برمجة الكود (Code API)

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // الحزمة المحلولة لمفتاح تفصيلي
Transport::authSource();                       // التطبيق المالك لإعدادات المصادقة، أو null
Transport::sharesAuthWith($guest, $host);      // فحص المصادقة بين التطبيقات
Transport::resolved();                         // جميع المفاتيح التفصيلية → النطاق
Transport::activeScenarios();                  // مثل ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## قاعدة البيانات

تستخدم الجداول ذات نطاق المنصة الاتصال **`platform`** والبادئة **`pinx_`**.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## وثائق ذات صلة

- [بيان app.php](../start/app-manifest.md)
- [إدارة المستخدمين](./user-management.md)
- [الوصول والصلاحيات](./access-permissions.md)
- [إدارة الملفات](./file-management.md)

---

[← العودة إلى الفهرس](../README.md)
