# Tutorial: aplicación web de agenda telefónica

[← Volver al índice](../README.md)

Construye una **aplicación web** simple con Twig: lista de contactos, formulario para añadir y eliminar entradas. Los datos se almacenan en MySQL.

**Package:** `com_acme_phonebook`  
**URL:** `http://localhost/pinoox/phonebook`  
**Código fuente completo:** [docs/source/phonebook-app/](../../source/phonebook-app/) — copiar a `apps/`
---

## Requisitos previos

- Pinoox instalado
- Conceptos básicos de [routers](../basic/routers.md) y [views](../basic/views.md)

---

## Paso 1 — Crear la app

```bash
php pinoox app:create com_acme_phonebook --simple
php pinoox app:router set /phonebook com_acme_phonebook
```

---

## Paso 2 — Tabla `contacts`

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

## Paso 3 — Modelo

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

## Paso 4 — Named Actions

`routes/actions.php`:

```php
<?php

use App\com_acme_phonebook\Controller\ContactController;
use function Pinoox\Router\action;

action('contact.list', [ContactController::class, 'index']);
action('contact.store', [ContactController::class, 'store']);
action('contact.delete', [ContactController::class, 'destroy']);
```

> **¿Por qué dos archivos de rutas?** En `actions.php` defines el handler **una vez**. En `web.php` solo mapeas URL + método HTTP a esa action (`@contact.list`). Cambia la URL sin tocar el cableado del controller.

---

## Paso 5 — Rutas web

`routes/web.php`:

```php
<?php

use function Pinoox\Router\{get, post};

get('/', '@contact.list')->name('home');
post('/add', '@contact.store')->name('contact.store');
post('/delete/{id}', '@contact.delete')->name('contact.delete');
```

---

## Paso 6 — Controller

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

## Paso 7 — Plantilla Twig (CSS inline)

Archivo `theme/default/pages/list.twig`:

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

## Paso 8 — Abrir en el navegador

```
http://localhost/pinoox/phonebook
```

Añade algunos contactos y prueba la eliminación.

---

## Paso 9 — Ideas para ampliar

| Mejora | Doc relacionada |
|---------|-------------|
| Editar contacto | [Controllers](../basic/controllers.md) |
| Búsqueda | [Query Builder](../database/query-builder.md) |
| Inicio de sesión | [Flows / Auth](../basic/flows.md) |
| API móvil | [Tutorial de API de notas](./simple-api-app.md) |

---

## Documentación relacionada

- [Migrations](../database/migrations.md)
- [Eloquent](../eloquent-orm/getting-started.md)
- [Validation](../basic/validation.md)

---

[← Volver al índice](../README.md)
