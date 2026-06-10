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
            return $this->fail('NOT_FOUND', 'کار یافت نشد.', status: 404);
        }

        $task->update(['status' => 'done']);

        return $this->ok($task);
    }

    public function destroy(Request $request, int $id)
    {
        $task = TaskModel::find($id);

        if (!$task) {
            return $this->fail('NOT_FOUND', 'کار یافت نشد.', status: 404);
        }

        $task->delete();

        return $this->ok(null, 'حذف شد.');
    }
}
