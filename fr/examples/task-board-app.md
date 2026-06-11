# Guide pas à pas : tableau de tâches (Todo)

[← Retour à l'index](../README.md)

Un **tableau de tâches quotidien** : ajouter des tâches, les marquer terminées et **filtrer** avec une query string (`?status=pending`). Pratique pour les panneaux internes et pour apprendre le filtrage par requête dans les contrôleurs.

**Paquet :** `com_acme_tasks`  
**URL :** `http://localhost/pinoox/tasks`  
**Source complète :** [docs/source/task-board-app/](../../source/task-board-app/) — copier vers `apps/`

---

## Prérequis

- [Requests](../basic/requests.md) — paramètres de requête
- [Base de données / Eloquent](../database/getting-started.md)

---

## Étape 1 — Créer l'app

```bash
php pinoox app:create com_acme_tasks --simple
php pinoox app:router set /tasks com_acme_tasks
```

---

## Étape 2 — Table `tasks`

```bash
php pinoox migrate:create CreateTasks com_acme_tasks
```

```php
<?php
namespace App\com_acme_tasks\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('tasks', 'com_acme_tasks'), function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('status', 20)->default('pending'); // pending | done
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('tasks', 'com_acme_tasks'));
    }
};
```

```bash
php pinoox migrate com_acme_tasks
```

---

## Étape 3 — Modèle

```bash
php pinoox model:create Task com_acme_tasks
```

```php
<?php
namespace App\com_acme_tasks\Model;

use Pinoox\Component\Database\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';

    protected $fillable = ['title', 'status'];

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDone($query)
    {
        return $query->where('status', 'done');
    }
}
```

---

## Étape 4 — Routes

`routes/actions.php` :

```php
<?php

use App\com_acme_tasks\Controller\TaskController;
use function Pinoox\Router\action;

action('task.list', [TaskController::class, 'index']);
action('task.store', [TaskController::class, 'store']);
action('task.done', [TaskController::class, 'markDone']);
action('task.reopen', [TaskController::class, 'reopen']);
```

`routes/web.php` :

```php
<?php

use function Pinoox\Router\{get, post};

get('/', '@task.list')->name('home');
post('/add', '@task.store')->name('task.store');
post('/done/{id}', '@task.done')->name('task.done');
post('/reopen/{id}', '@task.reopen')->name('task.reopen');
```

---

## Étape 5 — Contrôleur

```bash
php pinoox controller:create TaskController com_acme_tasks
```

```php
<?php
namespace App\com_acme_tasks\Controller;

use App\com_acme_tasks\Model\TaskModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\Controller;
use Pinoox\Portal\View;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('status', 'all');

        $query = TaskModel::query()->orderByDesc('id');

        if ($filter === 'pending') {
            $query->pending();
        } elseif ($filter === 'done') {
            $query->done();
        }

        return View::render('pages/board', [
            'title' => 'My tasks',
            'filter' => $filter,
            'tasks' => $query->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validate([
            'title' => 'required|string|max:255',
        ]);

        TaskModel::create([
            'title' => $data['title'],
            'status' => 'pending',
        ]);

        return redirect(url('/'));
    }

    public function markDone(Request $request, int $id)
    {
        $task = TaskModel::find($id);

        if ($task) {
            $task->update(['status' => 'done']);
        }

        return redirect(url('/'));
    }

    public function reopen(Request $request, int $id)
    {
        $task = TaskModel::find($id);

        if ($task) {
            $task->update(['status' => 'pending']);
        }

        return redirect(url('/'));
    }
}
```

> **Query string :** `$request->get('status')` lit `?status=pending` — aucune route supplémentaire nécessaire.

---

## Étape 6 — Modèle Twig (CSS inline)

`theme/default/pages/board.twig` :

```twig
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ title }}</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: system-ui, sans-serif; background: #f1f5f9; color: #0f172a; margin: 0; line-height: 1.5; }
        .page { max-width: 560px; margin: 0 auto; padding: 2rem 1rem; }
        .page-title { margin: 0 0 1.25rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; font-size: 1.5rem; }
        .panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; }
        .toolbar { display: flex; flex-wrap: wrap; gap: .5rem; margin-bottom: 1rem; }
        .toolbar a { padding: .35rem .85rem; border: 2px solid #cbd5e1; border-radius: 999px; text-decoration: none; color: #334155; font-size: .9rem; background: #fff; }
        .toolbar a.active { border-color: #334155; background: #334155; color: #fff; }
        .form-row { display: flex; gap: .5rem; }
        .form-row input { flex: 1; padding: .5rem .65rem; border: 2px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .btn { padding: .45rem 1rem; font: inherit; font-weight: 600; border-radius: 6px; cursor: pointer; background: transparent; border: 2px solid #2563eb; color: #2563eb; }
        .btn:hover { background: #2563eb; color: #fff; }
        .btn-sm { padding: .2rem .55rem; font-size: .85rem; border-color: #334155; color: #334155; }
        .task-list { list-style: none; padding: 0; margin: 0; }
        .task-list li { display: flex; align-items: center; gap: .5rem; padding: .65rem 0; border-bottom: 1px solid #e2e8f0; }
        .task-list li.done { opacity: .55; text-decoration: line-through; }
        form.inline { display: inline; margin: 0; }
        .empty { color: #64748b; font-style: italic; }
    </style>
</head>
<body>
<div class="page">
    <h1 class="page-title">{{ title }}</h1>
    <div class="panel">
        <div class="toolbar">
            Filter:
            <a href="{{ url('/') }}" class="{{ filter == 'all' ? 'active' : '' }}">All</a>
            <a href="{{ url('/?status=pending') }}" class="{{ filter == 'pending' ? 'active' : '' }}">Open</a>
            <a href="{{ url('/?status=done') }}" class="{{ filter == 'done' ? 'active' : '' }}">Done</a>
        </div>
        <form method="post" action="{{ url('add') }}" class="form-row">
            <input name="title" placeholder="New task…" required maxlength="255">
            <button type="submit" class="btn">Add</button>
        </form>
    </div>
    <div class="panel">
        <ul class="task-list">
            {% for task in tasks %}
            <li class="{{ task.status == 'done' ? 'done' : '' }}">
                <span style="flex:1">{{ task.title }}</span>
                {% if task.status == 'pending' %}
                <form class="inline" method="post" action="{{ url('done/' ~ task.id) }}"><button type="submit" class="btn btn-sm">Done</button></form>
                {% else %}
                <form class="inline" method="post" action="{{ url('reopen/' ~ task.id) }}"><button type="submit" class="btn btn-sm">Reopen</button></form>
                {% endif %}
            </li>
            {% else %}<li class="empty">No tasks yet.</li>{% endfor %}
        </ul>
    </div>
</div>
</body>
</html>
```

---

## Étape 7 — Tester

1. Ajoutez plusieurs tâches.
2. Marquez-en certaines terminées avec ✓.
3. Le filtre « Open » n'affiche que `pending`.
4. Utilisez ↩ pour rouvrir.

---

## Prochaines étapes

| Amélioration | Doc |
|---------|-----|
| Priorité / date d'échéance | [Mutators & casts](../eloquent-orm/mutators-casts.md) |
| API mobile | [Guide API Notes](./simple-api-app.md) |
| Connexion utilisateur | [Gestion des utilisateurs](../advanced/user-management.md) |

---

## Documentation associée

- [Contrôleurs](../basic/controllers.md)
- [Scopes Eloquent](../eloquent-orm/getting-started.md)

---

[← Retour à l'index](../README.md)
