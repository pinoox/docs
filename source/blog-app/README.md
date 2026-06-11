# Blog app

Source code for the Pinoox walkthrough: [Blog app](../../en/examples/blog-app.md)

**Package:** `com_acme_blog`
**URL path:** `/blog`

---

## Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/blog-app/com_acme_blog apps/com_acme_blog
   ```
   On Windows, copy `docs/source/blog-app/com_acme_blog` to `apps/com_acme_blog`.

2. Register the app route:
   ```bash
   php pinoox app:router set /blog com_acme_blog
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_blog
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/blog
   ```
