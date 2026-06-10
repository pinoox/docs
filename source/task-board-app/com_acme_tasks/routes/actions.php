<?php

use App\com_acme_tasks\Controller\TaskController;
use function Pinoox\Router\action;

action('task.list', [TaskController::class, 'index']);
action('task.store', [TaskController::class, 'store']);
action('task.done', [TaskController::class, 'markDone']);
action('task.reopen', [TaskController::class, 'reopen']);
