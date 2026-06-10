# Vite hybrid app / Vite hybrid

Source code for the Pinoox walkthrough: [Vite hybrid app](../../en/examples/vite-hybrid-app.md) · [Vite hybrid](../../fa/examples/vite-hybrid-app.md)

**Package:** `com_acme_vite_shop`
**URL path:** `/shop`

---

## English — Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/vite-hybrid-app/com_acme_vite_shop apps/com_acme_vite_shop
   ```
   On Windows, copy `docs/source/vite-hybrid-app/com_acme_vite_shop` to `apps/com_acme_vite_shop`.

2. Register the app route:
   ```bash
   php pinoox app:router set /shop com_acme_vite_shop
   ```
4. (Optional) Install and build frontend:
   ```bash
   cd apps/com_acme_vite_shop/theme/default
   npm install
   php pinoox fe com_acme_vite_shop build
   ```
5. Open in browser:
   ```
   http://localhost/pinoox/shop
   ```



---

## فارسی — نصب

۱. پوشه اپ را در پروژه کپی کنید:
   ```bash
   cp -r docs/source/vite-hybrid-app/com_acme_vite_shop apps/com_acme_vite_shop
   ```
   در ویندوز: `docs/source/vite-hybrid-app/com_acme_vite_shop` را به `apps/com_acme_vite_shop` کپی کنید.

۲. ثبت مسیر اپ:
   ```bash
   php pinoox app:router set /shop com_acme_vite_shop
   ```
۴. (اختیاری) نصب و build فرانت:
   ```bash
   cd apps/com_acme_vite_shop/theme/default
   npm install
   php pinoox fe com_acme_vite_shop build
   ```
۵. در مرورگر باز کنید:
   ```
   http://localhost/pinoox/shop
   ```
