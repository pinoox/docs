# Tutoriel pas à pas : Panneau SPA React

[← Retour à l'index](../README.md)

Un **panneau de tâches React 18** (Todo) — API en PHP, interface en React avec Vite. Point d'entrée React par défaut de Pinoox : `src/main.jsx`.

**Package :** `com_acme_react_tasks`  
**URL :** `http://localhost/pinoox/react-tasks`  
**Profil :** `spa` · **stack :** `react`  
**Source complète :** [docs/source/react-spa-app/](../../source/react-spa-app/) — à copier dans `apps/`
---

## Prérequis

- Node.js 18+ et npm
- [Requêtes (Requests)](../basic/requests.md) et [Réponses (Responses)](../basic/responses.md) pour les API JSON

---

## Étape 1 — Créer l'application

```bash
php pinoox app:create com_acme_react_tasks --simple
php pinoox app:router set /react-tasks com_acme_react_tasks
```

---

## Étape 2 — Table des tâches

```bash
php pinoox migrate:create CreateTasks com_acme_react_tasks
```

```php
<?php
namespace App\com_acme_react_tasks\database\migrations;

use Illuminate\Database\Schema\Blueprint;
use Pinoox\Component\Migration\MigrationBase;

return new class extends MigrationBase
{
    public function up()
    {
        $this->schema->create($this->table('tasks', 'com_acme_react_tasks'), function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $this->schema->dropIfExists($this->table('tasks', 'com_acme_react_tasks'));
    }
};
```

```bash
php pinoox migrate com_acme_react_tasks
php pinoox model:create Task com_acme_react_tasks
```

`Model/TaskModel.php` :

```php
<?php
namespace App\com_acme_react_tasks\Model;

use Pinoox\Component\Database\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $fillable = ['title', 'status'];
}
```

---

## Étape 3 — Controller d'API

```bash
php pinoox controller:create TaskApiController com_acme_react_tasks
```

```php
<?php
namespace App\com_acme_react_tasks\Controller;

use App\com_acme_react_tasks\Model\TaskModel;
use Pinoox\Component\Http\Request;
use Pinoox\Component\Kernel\Controller\ApiController;

class TaskApiController extends ApiController
{
    public function index()
    {
        return $this->ok(TaskModel::orderByDesc('id')->get());
    }

    public function store(Request $request)
    {
        $data = $this->validate(['title' => 'required|string|max:255']);

        $task = TaskModel::create([
            'title' => $data['title'],
            'status' => 'pending',
        ]);

        return $this->ok($task, status: 201);
    }

    public function done(Request $request, int $id)
    {
        $task = TaskModel::find($id);

        if (!$task) {
            return $this->fail('NOT_FOUND', 'Task not found.', status: 404);
        }

        $task->update(['status' => 'done']);

        return $this->ok($task);
    }

    public function destroy(Request $request, int $id)
    {
        $task = TaskModel::find($id);

        if (!$task) {
            return $this->fail('NOT_FOUND', 'Task not found.', status: 404);
        }

        $task->delete();

        return $this->ok(null, 'Deleted.');
    }
}
```

`routes/api.php` :

```php
<?php

use App\com_acme_react_tasks\Controller\TaskApiController;
use function Pinoox\Router\{collect, delete, get, patch, post, routes};

return routes([
    'version' => 'v1',
    'routes' => collect(function () {
        get('/tasks', [TaskApiController::class, 'index'])->name('tasks.index');
        post('/tasks', [TaskApiController::class, 'store'])->name('tasks.store');
        patch('/tasks/{id}/done', [TaskApiController::class, 'done'])->name('tasks.done');
        delete('/tasks/{id}', [TaskApiController::class, 'destroy'])->name('tasks.destroy');
    }),
]);
```

---

## Étape 4 — Générer le squelette React

```bash
php pinoox fe com_acme_react_tasks scaffold --stack=react
php pinoox fe com_acme_react_tasks install
```

`frontend.config.php` :

```php
<?php

return [
    'profile' => 'spa',
    'stack' => 'react',
    'entry' => 'src/main.jsx',
    'manifest' => 'dist/.vite/manifest.json',
    'mount' => '#app',
    'dev' => [
        'enabled' => (bool) _env('VITE_DEV', false),
        'url' => rtrim((string) _env('VITE_DEV_SERVER', 'http://127.0.0.1:5173'), '/'),
    ],
];
```

`app.php` :

```php
'theme' => 'default',
'frontend' => [
    'profile' => 'spa',
    'stack' => 'react',
],
'router' => [
    'routes' => [
        'routes/web.php',
        'routes/actions.php',
        'routes/api.php',
    ],
],
```

---

## Étape 5 — Route SPA

`routes/web.php` :

```php
<?php

use Pinoox\Portal\View;
use function Pinoox\Router\{get, routes};

return routes(function () {
    get('*', fn () => View::render('main'))->name('fallback');
});
```

---

## Étape 6 — Interface React (avec CSS)

`src/App.css` :

```css
*, *::before, *::after { box-sizing: border-box; }
.page { max-width: 560px; margin: 0 auto; padding: 2rem 1rem; font-family: system-ui, sans-serif; line-height: 1.5; color: #0f172a; }
.page-title { margin: 0 0 1.25rem; padding-bottom: .75rem; border-bottom: 2px solid #334155; font-size: 1.5rem; }
.panel { background: #fff; border: 2px solid #cbd5e1; border-radius: 10px; padding: 1.25rem 1.5rem; margin-bottom: 1rem; }
.form-row { display: flex; gap: .5rem; }
.form-row input { flex: 1; padding: .5rem .65rem; border: 2px solid #cbd5e1; border-radius: 6px; font: inherit; }
.btn { padding: .45rem 1rem; font: inherit; font-weight: 600; border-radius: 6px; cursor: pointer; background: transparent; border: 2px solid #334155; color: #334155; }
.btn-primary { border-color: #2563eb; color: #2563eb; }
.btn-primary:hover { background: #2563eb; color: #fff; }
.btn-sm { padding: .2rem .55rem; font-size: .85rem; margin-left: .35rem; }
.task-list { list-style: none; padding: 0; margin: 0; }
.task-list li { display: flex; align-items: center; gap: .5rem; padding: .65rem 0; border-bottom: 1px solid #e2e8f0; }
.task-list li.done { opacity: .55; text-decoration: line-through; }
.empty { color: #64748b; font-style: italic; margin: 0; }
```

`src/api/tasks.js` :

```js
import { getUrl } from '../boot.js';

const base = getUrl().API;

async function request(path, options = {}) {
    const res = await fetch(`${base}${path}`, {
        headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
        ...options,
    });
    const json = await res.json();
    if (!json.success) throw new Error(json.message || 'API error');
    return json.data;
}

export const listTasks = () => request('/tasks');
export const addTask = (title) => request('/tasks', { method: 'POST', body: JSON.stringify({ title }) });
export const markDone = (id) => request(`/tasks/${id}/done`, { method: 'PATCH' });
export const removeTask = (id) => request(`/tasks/${id}`, { method: 'DELETE' });
```

`src/App.jsx` :

```jsx
import { useEffect, useState } from 'react';
import { addTask, listTasks, markDone, removeTask } from './api/tasks.js';
import './App.css';

export default function App() {
    const [tasks, setTasks] = useState([]);
    const [title, setTitle] = useState('');
    const [loading, setLoading] = useState(true);

    async function reload() {
        setLoading(true);
        setTasks(await listTasks());
        setLoading(false);
    }

    useEffect(() => { reload(); }, []);

    async function handleAdd(e) {
        e.preventDefault();
        if (!title.trim()) return;
        await addTask(title.trim());
        setTitle('');
        await reload();
    }

    return (
        <main className="page">
            <h1 className="page-title">Tasks (React SPA)</h1>
            <div className="panel">
                <form className="form-row" onSubmit={handleAdd}>
                    <input value={title} onChange={(e) => setTitle(e.target.value)} placeholder="Task title" />
                    <button type="submit" className="btn btn-primary">Add</button>
                </form>
            </div>
            <div className="panel">
                {loading ? <p className="empty">Loading…</p> : (
                    <ul className="task-list">
                        {tasks.map((t) => (
                            <li key={t.id} className={t.status === 'done' ? 'done' : ''}>
                                <span style={{ flex: 1 }}>{t.title}</span>
                                {t.status !== 'done' && (
                                    <button type="button" className="btn btn-sm" onClick={() => markDone(t.id).then(reload)}>✓</button>
                                )}
                                <button type="button" className="btn btn-sm" onClick={() => removeTask(t.id).then(reload)}>×</button>
                            </li>
                        ))}
                    </ul>
                )}
            </div>
        </main>
    );
}
```

`src/main.jsx` :

```jsx
import { createRoot } from 'react-dom/client';
import App from './App.jsx';

createRoot(document.getElementById('app')).render(<App />);
```

---

## Étape 7 — `.env` et développement

`theme/default/.env` :

```env
VITE_SERVER_URL=http://localhost/pinoox/react-tasks
VITE_DEV=true
VITE_DEV_SERVER=http://127.0.0.1:5173
VITE_API_PATH=/api/v1/
```

```bash
php pinoox fe com_acme_react_tasks dev --no-serve
```

---

## Étape 8 — Build

```bash
php pinoox fe com_acme_react_tasks build
```

---

## Tester

1. `curl -s http://localhost/pinoox/react-tasks/api/v1/tasks`
2. Ouvrez la SPA ; ajoutez, terminez et supprimez des tâches.

---

## Comparaison avec la SPA Vue

| Sujet | React | Vue |
|-------|-------|-----|
| Point d'entrée | `src/main.jsx` | `src/main.js` |
| Scaffold | `--stack=react` | `--stack=vue` |
| Bootstrap | `getUrl()` dans `boot.js` | Identique |

---

## Documentation associée

- [Tutoriel SPA Vue](./vue-spa-app.md)
- [Tableau de tâches (Twig)](./task-board-app.md)
- [Templates](../basic/templates.md)

---

[← Retour à l'index](../README.md)
