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
