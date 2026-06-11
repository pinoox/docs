# Transport (gemeinsame Ressourcen)

[← Zurück zur Übersicht](../README.md)

In der HMVC-Architektur können Apps über den **`transport`**-Block in `app.php` Benutzer, Auth, Dateien und Berechtigungen miteinander teilen. Ohne Transport hält jede App jede Ressource **lokal** in ihrem eigenen Paket.

| Begriff | Bedeutung |
|------|---------|
| **`platform`** | Logischer gemeinsamer Scope — geteilte DB-Zeilen verwenden `app = platform` |
| **`pincore/`** | Nur der physische Framework-Ordner — **niemals** ein Wert für den Transport-Scope |

---

## Funktionsweise

Transport hat zwei Ebenen:

1. **Szenario** — ein Ein-Wort-Preset, das zu mehreren granularen Schlüsseln expandiert.
2. **Granularer Schlüssel** — ein mehrwortiger Name für eine spezifische gemeinsame Ressource.

```php
// app.php
'transport' => [
    'full' => 'platform',           // Szenario-Preset
    'file_storage' => 'local',      // granulares Override
],
```

**Auflösungsreihenfolge:** expliziter granularer Schlüssel → passendes Szenario.

Granulare Schlüssel gewinnen immer gegenüber der Szenario-Expansion. Wenn ein Schlüssel nicht gesetzt ist und kein Szenario ihn abdeckt, hält die App diese Ressource **lokal** (aktuelles Paket).

---

## Scope-Werte

Jedem Szenario oder granularen Schlüssel wird ein Scope zugewiesen:

| Scope | Bedeutung |
|-------|---------|
| `local` | Aktuelles App-Paket (Standard, wenn nicht angegeben) |
| `platform` | Gemeinsamer Plattform-Scope (`app = platform`, `pinx_*`-Tabellen) |
| `host` | App, die diese App geöffnet hat (Vorschau / `App::meeting()`) |
| `{package}` | Explizite App, z. B. `com_pinoox_manager` |

Für **`auth_config`** und **`auth_cookie`** lösen `platform` und `{package}` auf die App auf, die die **Auth-Einstellungen bereitstellt** (typischerweise `com_pinoox_manager`, falls installiert).

---

## Referenz: Szenarien

Ein-Wort-Presets. Verwendung in `app.php` als `'transport' => ['{scenario}' => '{scope}']`.

| Szenario | Beschreibung | Enthaltene granulare Schlüssel |
|----------|-------------|------------------------|
| `full` | Alle gemeinsamen Ressourcen | `user_table`, `auth_config`, `auth_cookie`, `session_token`, `file_storage`, `access_table` |
| `user` | Login-System: Konten, Auth, Session-Tokens | `user_table`, `auth_config`, `auth_cookie`, `session_token` |
| `storage` | Datei-Uploads und Metadaten | `file_storage` |
| `access` | Rollen und Berechtigungen | `access_table` |

---

## Referenz: granulare Schlüssel

Mehrwortige Ressourcennamen. Damit teilen oder überschreiben Sie eine einzelne Ressource.

| Granularer Schlüssel | Steuert | Verwendet von |
|--------------|----------|---------|
| `user_table` | `UserModel`-Spalte `app` / globaler Scope | Benutzerkonten |
| `auth_config` | Auth-Modus, JWT-Secret, Lifetimes (Quelle des `auth`-Blocks) | `AuthConfig`, Login-Flow |
| `auth_cookie` | Client-Schlüssel / Cookie-Name (`auth.key`) | Cookie- & SPA-Token-Speicherung |
| `session_token` | `TokenModel`-Spalte `app` / DB-Session-Zeilen | Session-Persistenz |
| `file_storage` | `FileModel`-Spalte `app` / Upload-Pfade | Uploads & Datei-Metadaten |
| `access_table` | `app`-Scope der Rollen- & Berechtigungsmodelle | `RoleModel`, `PermissionModel`, `can()` |

---

## Typische Konfigurationen

**Auth-Provider für die Plattform (z. B. Manager):**

```php
'transport' => ['full' => 'platform'],
'auth' => ['mode' => 'jwt', 'key' => 'manager_pinoox', /* … */],
```

**Consumer-App — alles geteilt, kein lokaler auth-Block:**

```php
'transport' => ['full' => 'platform'],
```

**Nur gemeinsamer Login:**

```php
'transport' => ['user' => 'platform'],
```

**Eigenständige App** — `transport` weglassen oder alles lokal festlegen:

```php
'transport' => ['user' => 'local'],
```

**Eine Ressource innerhalb eines Szenarios überschreiben:**

```php
'transport' => [
    'full' => 'platform',
    'file_storage' => 'local',
],
```

---

## Code-API

```php
use Pinoox\Component\Transport\TransportScenario;
use Pinoox\Portal\Transport;

Transport::package('user_table');              // aufgelöstes Paket für einen granularen Schlüssel
Transport::authSource();                       // App, der die Auth-Einstellungen gehören, oder null
Transport::sharesAuthWith($guest, $host);      // App-übergreifende Auth-Prüfung
Transport::resolved();                         // alle granularen Schlüssel → Scope
Transport::activeScenarios();                  // z. B. ['full']

TransportScenario::keysForScenario('user');
TransportScenario::scenariosForGranularKey('session_token');
TransportScenario::describes('full');
TransportScenario::granularLabels();
```

---

## Datenbank

Plattform-bezogene Tabellen verwenden die Connection **`platform`** und das Präfix **`pinx_`**.

```bash
php pinoox migrate platform
php pinoox patch:run platform
```

---

## Verwandte Dokumente

- [app.php-Manifest](../start/app-manifest.md)
- [Benutzerverwaltung (User management)](./user-management.md)
- [Zugriff & Berechtigungen](./access-permissions.md)
- [Dateiverwaltung (File management)](./file-management.md)

---

[← Zurück zur Übersicht](../README.md)
