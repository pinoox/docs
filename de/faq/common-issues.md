# Häufige Probleme

[← Zurück zum Index](../README.md)

Praktische Lösungen für häufige Fehler bei Installation, Laufzeit und Entwicklung mit Pinoox. Jeder Abschnitt empfiehlt **einen Ansatz**.

---

## `composer install` schlägt fehl

**Symptome:** fehlende Extension, niedrige PHP-Version oder Netzwerk-Timeout.

**Lösung:**

1. PHP 8.2+ und die Extensions `mysqli`, `zip`, `mbstring`, `json` aktivieren.
2. Vor der Installation die Plattformprüfung ausführen:

```bash
php launcher/check.php
```

3. Erneut installieren:

```bash
composer install --no-interaction
```

Auf Shared Hosting, wenn `composer` nicht im PATH ist, vendor lokal bauen und hochladen.

---

## Berechtigungsfehler (Dateizugriff)

**Symptome:** Kein Schreibzugriff auf `cache/`, `storage/`, `pinker/`.

**Lösung (Linux/macOS):**

```bash
chmod -R 775 cache storage pinker apps
chown -R www-data:www-data cache storage pinker
```

Der Webserver-Benutzer (z. B. `www-data` oder `apache`) muss in beschreibbare Ordner schreiben können. Unter Windows/MAMP das Projekt außerhalb von `Program Files` halten.

---

## `.htaccess` / Rewrite funktioniert nicht

**Symptome:** 404 auf allen URLs außer `index.php`; API liefert im Browser kein JSON.

**Lösung:**

1. Apache `mod_rewrite` aktivieren.
2. `AllowOverride All` für das DocumentRoot setzen.
3. Sicherstellen, dass `.htaccess` im Projektroot existiert.
4. Schnelltest: `http://localhost/pinoox/api/v1/ping` — bei JSON-Antwort funktioniert Rewrite.

Bei nginx `try_files` und `index.php`-Regeln in der Server-Config statt `.htaccess` schreiben.

---

## Datenbankverbindung schlägt fehl

**Symptome:** `SQLSTATE[HY000] [2002] Connection refused` oder Zugriff verweigert.

**Lösung:**

1. Sicherstellen, dass MySQL/MariaDB läuft.
2. Werte in `config/database.config.php` oder `.env` prüfen:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pinoox_db
DB_USERNAME=root
DB_PASSWORD=
```

3. Datenbank vorher anlegen (`CREATE DATABASE ... utf8mb4`).
4. Bei cPanel ist der Host möglicherweise nicht `localhost` — Hostnamen aus dem Panel verwenden.

---

## Pinker-Rebuild erforderlich

**Symptome:** veraltete Config oder Routen; Änderungen an `app.php` werden nicht übernommen.

**Lösung:**

```bash
php pinoox pinker:rebuild com_my_shop
# oder Alias:
php pinoox bake com_my_shop

# alle Apps:
php pinoox pinker:rebuild all
```

Nach Routen-, Config-Änderungen oder Produktions-Deploy ist meist ein Rebuild nötig.

---

## Route nicht gefunden (404 auf Endpunkt)

**Symptome:** Route ist im Code definiert, aber 404.

**Lösung:**

1. Sicherstellen, dass die Routendatei in `apps/{package}/routes/` liegt und in `app.php` → `router.routes` eingetragen ist.
2. URL mit App-Präfix abgleichen (`app:router`):

```bash
php pinoox app:router
php pinoox route:actions com_my_shop
```

3. Pinker-Rebuild ausführen (siehe oben).
4. Korrekte HTTP-Methode verwenden (`GET` vs. `POST`).

---

## 404 — App wird nicht aufgelöst

**Symptome:** Standardseite oder 404; falsche App wird geladen.

**Lösung:**

1. Pfad-/Host-Zuordnung prüfen:

```bash
php pinoox app:resolve --host=localhost --path=/shop
php pinoox app:domain
php pinoox app:router
```

2. Host und Pfad korrekt in `config/domain.config.php` (oder der relevanten Map) setzen.
3. `'enable' => true` in der `app.php` der App sicherstellen.
4. App-Ordnername muss `'package'` in `app.php` entsprechen (z. B. `com_my_shop`).

---

## Tests schlagen fehl

```bash
php pinoox test com_my_shop
```

- `.env.testing` mit separater DB
- Migrationen ausgeführt: `php pinoox migrate com_my_shop`
- nach `fakeApp()` → `deleteFakeApp()`

Details: [Erste Schritte beim Testen](../test/getting-started.md)

---

## Verwandte Dokumentation

- [Pinoox installieren](../start/installing-pinoox.md)
- [Projektstruktur](../start/structure.md)
- [Router](../basic/routers.md)
- [Config](../basic/config.md)
- [Pinoox Baker (Pinker)](../advanced/pinker.md)
- [Datenbank — Erste Schritte](../database/getting-started.md)
- [Support kontaktieren](./contact-support.md)

---

[← Zurück zum Index](../README.md)
