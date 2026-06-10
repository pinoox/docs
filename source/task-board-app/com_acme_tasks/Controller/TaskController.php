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
            'title' => 'کارهای من',
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
