# Pinoox installieren

[← Zurück zur Übersicht](../README.md)

Dieser Leitfaden behandelt die Installation von Pinoox 3.x. Es gibt zwei Wege zu starten:

| Weg | Am besten geeignet für |
|-------|----------|
| **A. Single-App mit [Pinx CLI](./pinx-cli.md)** | Erstellen einer einzelnen App — schnellster Start, ohne Manager-UI |
| **B. Vollständige Plattform (klassisch)** | Hosten mehrerer Apps mit grafischem Installer und Manager |

---

## Voraussetzungen

| Werkzeug | Version |
|------|---------|
| PHP | 8.1 oder höher (mit ext-mysqli, ext-zip) |
| MySQL / MariaDB | 5.7+ |
| Composer | 2.x |
| Node.js (optional) | 18+ — nur für Frontend-Theme-Builds |

---

## Weg A — Single-App mit Pinx CLI

Installieren Sie die [Pinx CLI](./pinx-cli.md) einmalig, erstellen Sie eine neue App und starten Sie sie:

```bash
composer global require pinoox/pinx-cli

pinx new my-shop              # schlägt com_my_shop vor — im Assistenten bestätigen oder anpassen
cd my-shop
cp .env.example .env          # DB_* setzen, falls Sie eine Datenbank verwenden
pinx setup                    # Plattform + App migrieren, Seeder ausführen
pinx dev                      # http://127.0.0.1:8000
```

Oder ohne globale Installation, über das Projekt-Template:

```bash
composer create-project pinoox/app my-shop
cd my-shop
cp .env.example .env
pinx setup
pinx dev
```

Führen Sie jederzeit `pinx doctor` aus, um PHP, Env, DB und Build-Bereitschaft zu prüfen. Siehe den vollständigen [Pinx-CLI-Leitfaden](./pinx-cli.md) für den täglichen Workflow und die Befehlsreferenz.

---

## Weg B — Vollständige Plattform (klassisch)

### 1. Projekt beziehen

```bash
git clone https://github.com/pinoox/pinoox.git
cd pinoox
composer install
```

Alternativ können Sie das neueste Release von [GitHub](https://github.com/pinoox/pinoox) herunterladen, entpacken und dann `composer install` ausführen.

---

### 2. Im Webserver ablegen

Legen Sie den Projektordner in Ihrem Document Root ab:

| Umgebung | Beispielpfad |
|-------------|--------------|
| MAMP | `C:/MAMP/htdocs/pinoox` |
| XAMPP | `C:/xampp/htdocs/pinoox` |
| WAMP | `C:/wamp64/www/pinoox` |

Setzen Sie den Document Root auf das **Projektstammverzeichnis** (den Ordner, der `index.php` enthält) — nicht auf einen `public`-Unterordner.

---

### 3. Datenbank erstellen

```sql
CREATE DATABASE pinoox_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### 4. Installer ausführen

Öffnen Sie Ihren Browser:

```
http://localhost/pinoox
```

Die System-App `com_pinoox_installer` startet. Die GUI-Schritte sind:

1. PHP-Anforderungen prüfen
2. Lizenzvereinbarung akzeptieren
3. Datenbank-Zugangsdaten eingeben
4. Admin-Konto erstellen
5. Installation abschließen

---

### 5. Nach der Installation

Hauptlayout:

```
pinoox/
├── index.php
├── pinoox              ← CLI
├── apps/               ← Apps
├── vendor/pinoox/pincore/  ← Core
└── config/             ← Projekt-Konfiguration
```

Erstellen Sie Ihre erste App:

```bash
php pinoox app:create com_acme_blog
```

---

## Schnelle Fehlerbehebung

| Problem | Lösung |
|---------|-----|
| Leere Seite | `composer install` ausführen und PHP-Fehlerprotokolle prüfen |
| 404 bei Unterrouten | mod_rewrite / `.htaccess` aktivieren |
| Fehler wegen fehlender Erweiterung | ext-mysqli und ext-zip in der php.ini aktivieren |
| Installer öffnet sich nicht | Document Root und Schreibrechte auf Laufzeitordnern prüfen |

---

## Verwandte Dokumente

- [Pinx CLI (Single-App)](./pinx-cli.md)
- [Ihre erste App](./your-first-app.md)
- [Projektstruktur](./structure.md)
- [Was ist Pinoox?](../introduction/what-is-pinoox.md)

---

[← Zurück zur Übersicht](../README.md)
