# Phonebook app

Source code for the Pinoox walkthrough: [Phonebook app](../../en/examples/phonebook-app.md)

**Package:** `com_acme_phonebook`
**URL path:** `/phonebook`

---

## Install

1. Copy the app folder into your project:
   ```bash
   cp -r docs/source/phonebook-app/com_acme_phonebook apps/com_acme_phonebook
   ```
   On Windows, copy `docs/source/phonebook-app/com_acme_phonebook` to `apps/com_acme_phonebook`.

2. Register the app route:
   ```bash
   php pinoox app:router set /phonebook com_acme_phonebook
   ```
3. Run migrations:
   ```bash
   php pinoox migrate com_acme_phonebook
   ```
4. Open in browser:
   ```
   http://localhost/pinoox/phonebook
   ```
