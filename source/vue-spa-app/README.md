# Vue SPA app / پنل Vue

Source code for the Pinoox walkthrough: [Vue SPA app](../../en/examples/vue-spa-app.md) · [پنل Vue](../../fa/examples/vue-spa-app.md)

**Package:** `com_acme_vue_notes`
**URL path:** `/vue-notes`

---

## English — Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/vue-spa-app/com_acme_vue_notes apps/com_acme_vue_notes
   ```
   On Windows, copy `docs/source/vue-spa-app/com_acme_vue_notes` to `apps/com_acme_vue_notes`.

2. Register the app route:
   ```bash
   php pinoox app:router set /vue-notes com_acme_vue_notes
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_vue_notes
   ```
4. (Optional) Install and build frontend:
   ```bash
   cd apps/com_acme_vue_notes/theme/default
   npm install
   php pinoox fe com_acme_vue_notes build
   ```
5. Open in browser:
   ```
   http://localhost/pinoox/vue-notes
   ```



---

## فارسی — نصب

۱. پوشه اپ را در پروژه کپی کنید:
   ```bash
   cp -r docs/source/vue-spa-app/com_acme_vue_notes apps/com_acme_vue_notes
   ```
   در ویندوز: `docs/source/vue-spa-app/com_acme_vue_notes` را به `apps/com_acme_vue_notes` کپی کنید.

۲. ثبت مسیر اپ:
   ```bash
   php pinoox app:router set /vue-notes com_acme_vue_notes
   ```
۳. اجرای migration:
   ```bash
   php pinoox migrate com_acme_vue_notes
   ```
۴. (اختیاری) نصب و build فرانت:
   ```bash
   cd apps/com_acme_vue_notes/theme/default
   npm install
   php pinoox fe com_acme_vue_notes build
   ```
۵. در مرورگر باز کنید:
   ```
   http://localhost/pinoox/vue-notes
   ```
