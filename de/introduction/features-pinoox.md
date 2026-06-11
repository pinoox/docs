# Pinoox-Features

[← Zurück zur Übersicht](../README.md)

Pinoox 3.x ist für ein modulares PHP-Ökosystem konzipiert: mehrere unabhängige Apps auf einem gemeinsamen Core, CLI-Scaffolding und eingebaute Werkzeuge für HTTP, Datenbank, Themes und Authentifizierung.

---

## HMVC-Architektur und unabhängige Apps

Jede App unter `apps/{package}/` hat eine vollständige MVC-Struktur:

| Schicht | Beispielpfad |
|-------|--------------|
| Controller | `Controller/MainController.php` |
| Model | `Model/PostModel.php` |
| View (Twig) | `theme/default/home.twig` |
| Route | `routes/web.php`, `routes/actions.php` |
| Flow (Middleware) | `Flow/AuthFlow.php` |

Das Hinzufügen oder Deaktivieren einer App beeinflusst die anderen nicht.

---

## CLI und schnelle Entwicklung

Aus dem Projektstammverzeichnis:

```bash
composer install
php pinoox app:create com_acme_blog
php pinoox controller:create PostController com_acme_blog
php pinoox migrate
```

Die CLI erzeugt das Standard-Ordnerlayout, `app.php` und die initialen Routendateien.

---

## Routing und Named Actions

URL-Pfade und logische Handler bleiben getrennt:

```php
// routes/actions.php
action('welcome', [MainController::class, 'home']);

// routes/web.php
get('/', '@welcome')->name('home');
```

Dieses Muster erleichtert Refactoring und Tests.

---

## Flow (Middleware)

Bevor eine Anfrage den Controller erreicht, werden Flows ausgeführt — für Authentifizierung, Autorisierung, Logging und mehr:

```php
get('panel', '@dashboard')->flows(['auth'])->name('panel');
```

Registrieren Sie Flow-Aliase in `app.php`.

---

## Views und Themes

- Twig-Templates in `theme/{themeName}/`
- Rendern mit **`View::render()`**
- SPA-Unterstützung mit Vite im Theme (Vue/React)

---

## Datenbank und Eloquent

- Query Builder und Eloquent über das `DB`-Portal
- Migrationen und Seeder im Ordner `database/migrations/` jeder App
- Tabellenpräfix basierend auf dem Paketnamen (z. B. `com_acme_blog_posts`)

---

## API und JSON-Antworten

Erweitern Sie **`ApiController`** und verwenden Sie das Standard-Envelope:

```php
return $this->ok($items);
return $this->fail('NOT_FOUND', 'Item not found.', status: 404);
```

---

## Internationalisierung

Übersetzungsdateien in `lang/{locale}/*.lang.php` — geeignet für mehrsprachige Apps.

---

## Verwandte Dokumente

- [Was ist Pinoox?](./what-is-pinoox.md)
- [Pinoox installieren](../start/installing-pinoox.md)
- [Router](../basic/routers.md)
- [Flow](../basic/flows.md)

---

[← Zurück zur Übersicht](../README.md)
