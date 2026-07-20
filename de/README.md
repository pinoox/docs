# Pinoox-Dokumentation

Offizielle Entwicklerdokumentation für das Erstellen von Apps auf der Pinoox-Plattform (PHP 8.2+, HMVC-Architektur).

Jeder Leitfaden beschreibt **einen empfohlenen Ansatz** mit praktischen Beispielen. Wählen Sie unten einen Abschnitt oder stöbern Sie nach Thema.

**Sprachen:** [English](../en/README.md) · [فارسی](../fa/README.md) · [العربية](../ar/README.md) · [中文](../zh/README.md) · [日本語](../ja/README.md) · [한국어](../ko/README.md) · [Türkçe](../tr/README.md) · [Español](../es/README.md) · [हिन्दी](../hi/README.md) · [Français](../fr/README.md) · [Русский](../ru/README.md) · [Português](../pt/README.md) · [Deutsch](./README.md)

---

### Einführung

#### [Was ist Pinoox?](./introduction/what-is-pinoox.md)
#### [Pinoox-Features](./introduction/features-pinoox.md)
#### [Zu Pinoox beitragen](./introduction/contributions.md)

### Erste Schritte

#### [Pinoox installieren](./start/installing-pinoox.md)
#### [Ihre erste App](./start/your-first-app.md)
#### [Projektstruktur](./start/structure.md)
#### [Pinoox-CLI-Referenz](./start/cli-reference.md)
#### [Pinx CLI (Single-App-Projekte)](./start/pinx-cli.md)
#### [app.php-Manifest-Referenz](./start/app-manifest.md)

### Praktische Anleitungen

#### [Walkthrough: Notes-API-App](./examples/simple-api-app.md)
#### [Walkthrough: Telefonbuch-Web-App](./examples/phonebook-app.md)
#### [Walkthrough: Kontaktformular-App](./examples/contact-form-app.md)
#### [Walkthrough: Einfache Blog-App](./examples/blog-app.md)
#### [Walkthrough: Task-Board (Todo)](./examples/task-board-app.md)
#### [Walkthrough: Bildergalerie-App](./examples/gallery-app.md)
#### [Walkthrough: Vue-SPA-Panel](./examples/vue-spa-app.md)
#### [Walkthrough: React-SPA-Panel](./examples/react-spa-app.md)
#### [Walkthrough: Vite-Hybrid (Twig + JS-Widget)](./examples/vite-hybrid-app.md)

### Kernkonzepte

#### [Router](./basic/routers.md)
#### [Controller](./basic/controllers.md)
#### [Flow (Middleware)](./basic/flows.md)
#### [HTTP-Request](./basic/requests.md)
#### [HTTP-Response](./basic/responses.md)
#### [URL- und Link-Erstellung](./basic/url.md)
#### [Dateipfade](./basic/path.md)
#### [Validierung](./basic/validation.md)
#### [Views](./basic/views.md)
#### [Twig-Templates](./basic/templates.md)
#### [Portal (Facade)](./basic/portal.md)
#### [Config](./basic/config.md)
#### [Sprache und Übersetzung](./basic/language.md)

### Fortgeschrittene Themen

#### [Pinker und Cache](./advanced/pinker.md)
#### [Patches (Datenaktualisierungen)](./advanced/patches.md)

#### [App-Services (Component + Portal)](./advanced/services.md)
#### [Globale Helpers](./advanced/helpers.md)
#### [E-Mails versenden](./advanced/mail.md)
#### [HTTP-Client](./advanced/http-client.md)
#### [Benutzerverwaltung (User Management)](./advanced/user-management.md)
#### [Dateiverwaltung (File Management)](./advanced/file-management.md)
#### [Pinion-Protokoll](./advanced/pinion.md)
#### [Token-Verwaltung](./advanced/token-management.md)
#### [Zugriff & Berechtigungen](./advanced/access-permissions.md)
#### [Transport (gemeinsame Ressourcen)](./advanced/transport.md)
#### [boot.php und Events](./advanced/boot-and-events.md)
#### [Zeitplanung (Cron)](./advanced/schedule.md)

### Datenbank

#### [Erste Schritte mit der Datenbank](./database/getting-started.md)
#### [Query Builder](./database/query-builder.md)
#### [Paginierung](./database/pagination.md)
#### [Migrationen](./database/migrations.md)

### Eloquent ORM

#### [Eloquent ORM — Erste Schritte](./eloquent-orm/getting-started.md)
#### [Eloquent-Beziehungen](./eloquent-orm/relationships.md)
#### [Eloquent Collections](./eloquent-orm/collections.md)
#### [Mutatoren und Casts](./eloquent-orm/mutators-casts.md)
#### [API-Ressourcen](./eloquent-orm/api-resources.md)
#### [Model-Serialisierung](./eloquent-orm/serialization.md)
#### [Testdaten — Seeder](./eloquent-orm/factories.md)

### Testen

#### [Erste Schritte beim Testen in Pinoox](./test/getting-started.md)
#### [HTTP-Tests in Pinoox](./test/http-tests.md)
#### [Konsolen-Tests in Pinoox](./test/console-tests.md)
#### [Browser- (HTML-)Tests in Pinoox](./test/browser-tests.md)
#### [Datenbank-Tests in Pinoox](./test/database.md)
#### [Serialisierungstests in Pinoox](./test/serialization.md)
#### [Mocking in Pinoox](./test/mocking.md)

### FAQ

#### [Häufige Probleme](./faq/common-issues.md)
#### [Support kontaktieren](./faq/contact-support.md)

---

### Quellcode
**Beispiel-Quellcode:** [docs/source/](../source/) — vollständiger Code für jede Anleitung

Schritt-für-Schritt-Anleitungen für echte Apps — nach den Grundlagen, wenn Sie praxisnahen Code möchten.

---

### Wie man diese Dokumentation liest

1. Beginnen Sie mit **Einführung** und **Erste Schritte**, wenn Sie neu bei Pinoox sind.
2. Folgen Sie den **Praktischen Anleitungen** — bauen Sie Schritt für Schritt eine JSON-API und eine einfache Website.
3. Lesen Sie die **Kernkonzepte**, während Sie Routen, Controller und Views erstellen.
4. Nutzen Sie **Datenbank** und **Eloquent ORM**, wenn Sie Persistenz hinzufügen.
5. Schlagen Sie in den **Fortgeschrittenen Themen** für Auth, Dateien, Pinker und gemeinsame Services nach.
6. Verwenden Sie **Testen**, bevor Sie Features in die Produktion bringen.

Der gesamte App-Code liegt unter `apps/{package}/`. Der Framework-Kern ist `vendor/pinoox/pincore/` — legen Sie dort keine App-Geschäftslogik ab.
