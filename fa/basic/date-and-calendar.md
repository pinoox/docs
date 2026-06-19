# تاریخ و تقویم

[← بازگشت به فهرست](../README.md)

در پینوکس ۳.x کامپوننت یکپارچه **Date** داخل pincore قرار دارد. اپ‌ها نیازی به نصب جداگانه Jalali یا Carbon ندارند — از Portal `Date` یا helperهای سراسری استفاده کنید.

---

## تنظیم تقویم در `app.php`

تقویم فعال را یک‌بار در manifest اپ مشخص کنید. هنگام بارگذاری manifest، pincore فرم‌های کوتاه را به ساختار استاندارد تبدیل می‌کند.

```php
// ساده‌ترین حالت
'date' => 'jalali',

// با timezone
'date' => [
    'calendar' => 'jalali',
    'timezone' => 'Asia/Tehran',
],

// alias در ریشه manifest
'calendar' => 'jalali',
'timezone' => 'Asia/Tehran',
```

| مقدار | معنی |
|-------|------|
| `jalali` | تقویم شمسی (نام‌های مستعار: `shamsi`, `jalaali`) |
| `gregorian` | تقویم میلادی (نام‌های مستعار: `miladi`, `g`) |

اگر `date.calendar` تنظیم نشود، pincore از `lang` → `locale_calendar` در `date.config.php` و سپس پیش‌فرض پلتفرم (`gregorian`) استفاده می‌کند.

جزئیات بیشتر: [مرجع app.php — date](../start/app-manifest.md#تاریخ-و-timezone).

---

## Portal تاریخ

```php
use Pinoox\Portal\Date;

// از تقویم app.php استفاده می‌کند
Date::display($createdAt);           // کلید datetime
Date::display($createdAt, 'date');   // Y/m/d یا Y-m-d
Date::smart($createdAt);
Date::calendar();                    // jalali | gregorian
Date::timezone();

// تقویم صریح
Date::jalali($time)->format('l d F Y');
Date::gregorian($time)->format('Y-m-d');
Date::make($time);
Date::parseJalali('1403-01-15', 'Y-m-d');

// override موقت (تنظیمات اپ را عوض نمی‌کند)
Date::usingCalendar('gregorian')->format($time);
```

`Date` متدهای شبیه Carbon هم دارد: `now()`, `today()`, `parse()`, مقایسه تاریخ، و برچسب تقریبی (`approximateDate` / `date_ago`).

---

## Helperهای سراسری

از `pincore/functions/date.php` بارگذاری می‌شوند:

| Helper | کاربرد |
|--------|--------|
| `now()`, `today()`, `carbon()` | نمونه Carbon از Date |
| `jalali()`, `gregorian()` | آبجکت تقویم |
| `date_make()` | نمونه برای تقویم فعال یا مشخص‌شده |
| `date_display()`, `date_smart()`, `format_date()` | رشته فرمت‌شده |
| `jformat()`, `format_jalali()`, `gdate()` | میانبر فرمت |
| `date_ago()` | برچسب نسبی / تقریبی |

```php
return date_display($post->created_at, 'datetime');
return jformat($order->paid_at, 'Y/m/d H:i');
```

---

## تنظیمات پلتفرم

پیش‌فرض‌ها در `vendor/pinoox/pincore/config/date.config.php`:

- `timezone` — با `DATE_TIMEZONE` در `.env` یا `app.php → date.timezone`
- `calendar` — با `DATE_CALENDAR` یا `app.php`
- `formats` — قالب‌های نمایش (`date`, `datetime`, `time`, `full`)
- `locale_calendar` — وقتی اپ `date.calendar` ندارد (`fa` → jalali)

---

## نکات

- در API و Twig از `Date::display()` استفاده کنید تا تقویم اپ رعایت شود.
- در دیتابیس timestamp یا تاریخ میلادی ذخیره کنید؛ هنگام نمایش فرمت کنید.
- `morilog/jalali` یا `nesbot/carbon` را در `composer.json` اپ اضافه نکنید — pincore آن‌ها را فراهم می‌کند.

---

## مستندات مرتبط

- [مرجع app.php](../start/app-manifest.md)
- [Portal](./portal.md)
- [Helperهای سراسری](../advanced/helpers.md)
- [زبان و ترجمه](./language.md)

---

[← بازگشت به فهرست](../README.md)
