# ترنسپورت (Transport) — منابع مشترک

[← بازگشت به فهرست](../README.md)

در معماری HMVC، اپ‌ها می‌توانند کاربران، احراز هویت، فایل‌ها و مجوزها را از طریق بلوک **`transport`** در `app.php` با هم به اشتراک بگذارند. بدون transport، هر اپ همه منابع را **local** (مخصوص پکیج خودش) نگه می‌دارد.

| اصطلاح | معنی |
|--------|------|
| **`platform`** | اسکوپ منطقی مشترک — رکوردهای مشترک DB با `app = platform` ذخیره می‌شوند |
| **`pincore/`** | فقط پوشه فیزیکی فریمورک — **هرگز** به‌عنوان مقدار scope در transport استفاده نکنید |

---

## نحوه کارکرد

Transport دو لایه دارد:

1. **سناریو (Scenario)** — یک preset تک‌کلمه‌ای که به چند کلید granular گسترش می‌یابد.
2. **کلید granular** — نام چندکلمه‌ای برای یک منبع مشترک مشخص.

```php
// app.php
'transport' => [
    'full' => 'platform',           // preset سناریو
    'file_storage' => 'local',      // override جزئی
],
```

**ترتیب resolve:** کلید granular صریح ← سپس سناریوی منطبق.

کلیدهای granular همیشه بر گسترش سناریو اولویت دارند. اگر کلیدی تنظیم نشده باشد و هیچ سناریویی آن را پوشش ندهد، آن منبع برای اپ **local** (پکیج جاری) باقی می‌ماند.

---

## مقادیر Scope

به هر سناریو یا کلید granular یک scope اختصاص می‌یابد:

| Scope | معنی |
|-------|------|
| `local` | پکیج اپ جاری (پیش‌فرض در صورت حذف) |
| `platform` | اسکوپ مشترک پلتفرم (`app = platform`، جداول `pinx_*`) |
| `host` | اپی که این اپ را باز کرده است (preview / `App::meeting()`) |
| `{package}` | اپ مشخص، مثلاً `com_pinoox_manager` |

برای **`auth_config`** و **`auth_cookie`**، مقادیر `platform` و `{package}` به اپی resolve می‌شوند که **تنظیمات auth را تامین می‌کند** (معمولاً `com_pinoox_manager` در صورت نصب).

---

## مرجع سناریوها

preset های تک‌کلمه‌ای. در `app.php` به‌صورت `'transport' => ['{scenario}' => '{scope}']` استفاده کنید.

| سناریو | توضیح | کلیدهای granular شامل |
|--------|-------|------------------------|
| `full` | همه منابع مشترک | `user_table`، `auth_config`، `auth_cookie`، `session_token`، `file_storage`، `access_table` |
| `user` | سیستم لاگین: حساب‌ها، auth، توکن نشست | `user_table`، `auth_config`، `auth_cookie`، `session_token` |
| `storage` | آپلود فایل و متادیتا | `file_storage` |
| `access` | نقش‌ها و مجوزها | `access_table` |

---

## مرجع کلیدهای granular

نام منابع چندکلمه‌ای. برای اشتراک یا override یک منبع مشخص استفاده کنید.

| کلید granular | کنترل می‌کند | استفاده‌کننده |
|---------------|--------------|----------------|
| `user_table` | ستون `app` در `UserModel` / اسکوپ سراسری | حساب‌های کاربری |
| `auth_config` | مود auth، رمز JWT، طول عمر (منبع بلوک `auth`) | `AuthConfig`، فرایند لاگین |
| `auth_cookie` | کلید کلاینت / نام کوکی (`auth.key`) | ذخیره توکن کوکی و SPA |
| `session_token` | ستون `app` در `TokenModel` / رکوردهای نشست در DB | ماندگاری نشست |
| `file_storage` | ستون `app` در `FileModel` / مسیرهای آپلود | آپلودها و متادیتای فایل |
| `access_table` | اسکوپ `app` مدل‌های نقش و مجوز | `RoleModel`، `PermissionModel`، `can()` |

---

## تنظیمات رایج

**اپ تامین‌کننده auth برای پلتفرم (مثل manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**اپ مصرف‌کننده — همه‌چیز مشترک، بدون بلوک auth محلی:**

```php
'transport' => ['full' => 'platform'],
```

**فقط لاگین مشترک:**

```php
'transport' => ['user' => 'platform'],
```

**اپ مستقل** — `transport` را حذف کنید یا همه‌چیز را local نگه دارید:

```php
'transport' => ['user' => 'local'],
```

**Override یک منبع داخل سناریو:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## API کد

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // پکیج resolve شده برای یک کلید granular
Transport::authSource();                       // اپ مالک تنظیمات auth یا null
Transport::sharesAuthWith($guest, $host);      // بررسی auth مشترک بین دو اپ
Transport::resolved();                         // همه کلیدهای granular → scope
Transport::activeScenarios();                  // مثلاً ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## دیتابیس

جداول با اسکوپ پلتفرم از connection **`platform`** و پیشوند **`pinx_`** استفاده می‌کنند.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## مستندات مرتبط

- [مرجع app.php](../start/app-manifest.md)
- [مدیریت کاربران](./user-management.md)
- [دسترسی و مجوز](./access-permissions.md)
- [مدیریت فایل](./file-management.md)

---

[← بازگشت به فهرست](../README.md)
