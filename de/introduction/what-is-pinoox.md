# Was ist Pinoox?

[← Zurück zum Index](../README.md)

Pinoox ist ein modernes, quelloffenes PHP-Framework (3.x), das auf der HMVC-Architektur und dem **App**-Konzept aufbaut. Es macht modulare Webentwicklung unkompliziert: Jede App ist eine unabhängige MVC-Einheit unter `apps/{package}/`, während der gemeinsame Framework-Kern in `vendor/pinoox/pincore/` liegt.

---

## App-zentrierte Architektur

In einer einzigen Pinoox-Installation laufen mehrere unabhängige Apps nebeneinander:

```
{project_root}/
├── index.php              ← Web-Einstiegspunkt
├── pinoox                 ← CLI-Einstiegspunkt
├── composer.json
├── vendor/pinoox/pincore/ ← Framework-Kern (nur für Core-Änderungen bearbeiten)
└── apps/
    ├── com_pinoox_manager/
    └── com_example_blog/  ← Ihre App
```

- **Projekt** — der Ordner, der `index.php` und `apps/` enthält (der Ordnername spielt keine Rolle).
- **App** — ein vollständiges Modul mit eigenen Controllern, Models, Routen, Theme und Konfiguration.
- **Core** — die gemeinsame Engine (Router, HTTP, Datenbank, Twig, CLI und mehr).

Schreiben Sie Geschäftslogik in `apps/`, nicht in `vendor/pinoox/pincore/`.

---

## Lebenszyklus einer HTTP-Anfrage

```
Webbrowser → index.php → Bootstrapping
          → aktive App auflösen (Domain oder URL-Präfix)
          → app.php und routes/ laden
          → Flows → Controller → Model (optional) → Ansicht oder JSON
```

---

## App-Benennung

Empfohlenes Paketformat:

```
com_{vendor}_{name}
```

Beispiel: `com_acme_shop` — der Ordnername, der `package`-Wert in `app.php` und das Namespace-Segment müssen alle übereinstimmen.

---

## Gut geeignet für

- Websites mit mehreren Bereichen und Admin-Panels, bei denen jeder Bereich eine eigene App sein kann
- Teams, die Module unabhängig entwickeln, testen und warten möchten
- PHP-8.1+-Projekte mit Composer und der integrierten CLI (`php pinoox …`)

---

## Verwandte Dokumente

- [Pinoox-Features](./features-pinoox.md)
- [Pinoox installieren](../start/installing-pinoox.md)
- [Ihre erste App](../start/your-first-app.md)
- [Notes-API-Anleitung](../examples/simple-api-app.md)
- [Telefonbuch-Anleitung](../examples/phonebook-app.md)
- [Kontaktformular-Anleitung](../examples/contact-form-app.md)
- [Projektstruktur](../start/structure.md)

---

[← Zurück zum Index](../README.md)
