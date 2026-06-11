# Vite hybrid app

Source code for the Pinoox walkthrough: [Vite hybrid app](../../en/examples/vite-hybrid-app.md)

**Package:** `com_acme_vite_shop`
**URL path:** `/shop`

---

## Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/vite-hybrid-app/com_acme_vite_shop apps/com_acme_vite_shop
   ```
   On Windows, copy `docs/source/vite-hybrid-app/com_acme_vite_shop` to `apps/com_acme_vite_shop`.

2. Register the app route:
   ```bash
   php pinoox app:router set /shop com_acme_vite_shop
   ```
3. (Optional) Install and build frontend:
   ```bash
   cd apps/com_acme_vite_shop/theme/default
   npm install
   php pinoox fe com_acme_vite_shop build
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/shop
   ```
