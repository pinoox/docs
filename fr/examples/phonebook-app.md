# Tutoriel pas à pas : Application web de répertoire téléphonique

[← Retour à l'index](../README.md)

Construisez une **application web** simple avec Twig : lister les contacts, ajouter via un formulaire et supprimer des entrées. Les données sont stockées dans MySQL.

**Package :** `com_acme_phonebook`  
**URL :** `http://localhost/pinoox/phonebook`  
**Source complète :** [docs/source/phonebook-app/](../../source/phonebook-app/) — à copier dans `apps/`
---

## Prérequis

- Pinoox installé
- Notions de base sur les [routeurs (routers)](../basic/routers.md) et les [vues (views)](../basic/views.md)

---

## Étape 1 — Créer l'application

```bash
php pinoox app:create com_acme_phonebook --simple
php pinoox app:router set /phonebook com_acme_phonebook
```

---

## Étape 2 — Table `contacts`

```bash
php pinoox migrate:create CreateContacts com_acme_phonebook
```

```php
<?php
namespace App\com_acme_phonebook\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('contacts', 'com_acme_phonebook'), function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('phone', 30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('contacts', 'com_acme_phonebook'));
    }
};
```

```bash
php pinoox migrate com_acme_phonebook
```

---

## Étape 3 — Modèle (Model)

```bash
php pinoox model:create Contact com_acme_phonebook
```

```php
<?php
namespace App\com_acme_phonebook\Model;

use Pinoox\Component\Database\Model;

class ContactModel extends Model
{
    protected $table = 'contacts';

    protected $fillable = ['name', 'phone'];
}
```

---

## Étape 4 — Actions nommées

`routes/actions.php` :

```php
<?php

use App\com_acme_phonebook\Controller\ContactController;
use function Pinoox\Router\action;

action('contact.list', [ContactController::class, 'index']);
action('contact.store', [ContactController::class, 'store']);
action('contact.delete', [ContactController::class, 'destroy']);
```

> **Pourquoi deux fichiers de routes ?** Dans `actions.php`, vous définissez le gestionnaire (handler) **une seule fois**. Dans `web.php`, vous ne faites qu'associer l'URL + la méthode HTTP à cette action (`@contact.list`). Vous pouvez changer l'URL sans toucher au câblage du Controller.

---

## Étape 5 — Routes web

`routes/web.php` :

```php
<?php

use function Pinoox\Router\{get, post};

get('/', '@contact.list')->name('home');
post('/add', '@contact.store')->name('contact.store');
post('/delete/{id}', '@contact.delete')->name('contact.delete');
```

---

## Étape 6 — Controller

```bash
php pinoox controller:create ContactController com_acme_phonebook
```

```php
<?php
namespace App\com_acme_phonebook\Controller;

use App\com_acme_phonebook\Model\ContactModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class ContactController extends Controller
{
    public function index()
    {
        return View::render('pages/list', [
            'title' => 'Phonebook',
            'contacts' => ContactModel::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:30',
        ]);

        ContactModel::create($data);

        return redirect(url('/'));
    }

    public function destroy(Request $request, int $id)
    {
        $contact = ContactModel::find($id);

        if ($contact) {
            $contact->delete();
        }

        return redirect(url('/'));
    }
}
```

---

## Étape 7 — Template Twig (CSS en ligne)

Fichier `theme/default/pages/list.twig` :

```twig
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; color: #0f172a; margin: 0; line-height: 1.5; }
        .page { max-width: 720px; margin: 0 auto; padding: 2rem 1rem; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; }
        .page-title { margin: 0 0 1.5rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; font-size: 1.5rem; }
        .subtitle { margin: 0 0 1rem; font-size: 1.1rem; }
        .field { margin-bottom: 1rem; }
        .field label { display: block; font-weight: 600; margin-bottom: .35rem; font-size: .9rem; }
        .field input { width: 100%; padding: .5rem .65rem; border: 2px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .btn { display: inline-block; padding: .45rem 1rem; font: inherit; font-weight: 600; border-radius: 6px; cursor: pointer; background: transparent; border: 2px solid #334155; color: #334155; }
        .btn-primary { border-color: #2563eb; color: #2563eb; }
        .btn-primary:hover { background: #2563eb; color: #fff; }
        .btn-danger { border-color: #dc2626; color: #dc2626; font-size: .85rem; padding: .25rem .6rem; }
        .btn-danger:hover { background: #dc2626; color: #fff; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 2px solid #cbd5e1; padding: .6rem .75rem; text-align: left; }
        .table th { background: #f8fafc; }
        .empty { color: #64748b; font-style: italic; }
        form.inline { display: inline; margin: 0; }
    </style>
</head>
<body>
<div class="page">
    <h1 class="page-title">{{ title }}</h1>
    <div class="panel">
        <h2 class="subtitle">Add contact</h2>
        <form method="post" action="{{ url('add') }}">
            <div class="field"><label>Name</label><input name="name" required maxlength="120"></div>
            <div class="field"><label>Phone</label><input name="phone" required maxlength="30"></div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
    <div class="panel">
        <h2 class="subtitle">Contacts</h2>
        <table class="table">
            <thead><tr><th>Name</th><th>Phone</th><th></th></tr></thead>
            <tbody>
                {% for c in contacts %}
                <tr>
                    <td>{{ c.name }}</td><td>{{ c.phone }}</td>
                    <td><form method="post" action="{{ url('delete/' ~ c.id) }}" class="inline"><button type="submit" class="btn btn-danger">Delete</button></form></td>
                </tr>
                {% else %}<tr><td colspan="3" class="empty">No contacts yet.</td></tr>{% endfor %}
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
```

---

## Étape 8 — Ouvrir dans le navigateur

```
http://localhost/pinoox/phonebook
```

Ajoutez quelques contacts et essayez la suppression.

---

## Étape 9 — Idées d'extension

| Amélioration | Doc associée |
|---------|-------------|
| Modifier un contact | [Controllers](../basic/controllers.md) |
| Recherche | [Query Builder](../database/query-builder.md) |
| Connexion (login) | [Flows / Auth](../basic/flows.md) |
| API mobile | [Tutoriel API de notes](./simple-api-app.md) |

---

## Documentation associée

- [Migrations](../database/migrations.md)
- [Eloquent](../eloquent-orm/getting-started.md)
- [Validation](../basic/validation.md)

---

[← Retour à l'index](../README.md)
