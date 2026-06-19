# Date and calendar

[← Back to index](../README.md)

Pinoox 3.x ships a unified **Date** component in pincore. Apps do not need separate Jalali or Carbon packages — use the `Date` Portal or global helpers.

---

## Configure calendar in `app.php`

Set the active calendar once in your app manifest. Pincore normalizes shorthand forms when the manifest loads.

```php
// simplest
'date' => 'jalali',

// or with timezone
'date' => [
    'calendar' => 'jalali',
    'timezone' => 'Asia/Tehran',
],

// root aliases (merged into date.*)
'calendar' => 'jalali',
'timezone' => 'Asia/Tehran',
```

| Value | Meaning |
|-------|---------|
| `jalali` | Shamsi calendar (aliases: `shamsi`, `jalaali`) |
| `gregorian` | Gregorian calendar (aliases: `miladi`, `g`) |

If `date.calendar` is omitted, pincore uses `lang` → `locale_calendar` from `date.config.php`, then the platform default (`gregorian`).

See also: [app.php manifest — date](../start/app-manifest.md#date-and-timezone).

---

## Date Portal

```php
use Pinoox\Portal\Date;

// Uses app.php calendar automatically
Date::display($createdAt);           // preset key: datetime
Date::display($createdAt, 'date');     // Y/m/d or Y-m-d
Date::smart($createdAt);               // format for active calendar
Date::calendar();                      // jalali | gregorian
Date::timezone();                      // app or platform timezone

// Explicit calendar
Date::jalali($time)->format('l d F Y');
Date::gregorian($time)->format('Y-m-d');
Date::make($time);                     // active calendar instance
Date::parseJalali('1403-01-15', 'Y-m-d');

// Temporary override (does not change app config)
Date::usingCalendar('gregorian')->format($time);
```

`Date` also exposes Carbon-style helpers: `now()`, `today()`, `parse()`, comparison helpers, and approximate labels (`approximateDate` / `date_ago`).

---

## Global helpers

Loaded from `pincore/functions/date.php`:

| Helper | Purpose |
|--------|---------|
| `now()`, `today()`, `carbon()` | Carbon instances via Date |
| `jalali()`, `gregorian()` | Calendar value objects |
| `date_make()` | Instance for active or given calendar |
| `date_display()`, `date_smart()`, `format_date()` | Formatted strings |
| `jformat()`, `format_jalali()`, `gdate()` | Shortcut formatters |
| `date_ago()` | Relative / approximate label |

```php
return date_display($post->created_at, 'datetime');
return jformat($order->paid_at, 'Y/m/d H:i');
```

---

## Platform config

Defaults live in `vendor/pinoox/pincore/config/date.config.php`:

- `timezone` — override with `DATE_TIMEZONE` in `.env` or `app.php → date.timezone`
- `calendar` — override with `DATE_CALENDAR` or `app.php`
- `formats` — display presets per calendar (`date`, `datetime`, `time`, `full`)
- `locale_calendar` — fallback when app does not set `date.calendar` (`fa` → jalali)

---

## Tips

- Prefer `Date::display()` in APIs and Twig data — it respects the app calendar without extra logic.
- Store timestamps or Gregorian datetimes in the database; format at display time.
- Do not add `morilog/jalali` or `nesbot/carbon` to app `composer.json`; pincore already provides them.

---

## Related docs

- [app.php manifest](../start/app-manifest.md)
- [Portal](./portal.md)
- [Global helpers](../advanced/helpers.md)
- [Language and translation](./language.md)

---

[← Back to index](../README.md)
