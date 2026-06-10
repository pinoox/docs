<?php

use App\com_acme_notes\Controller\MainController;
use function Pinoox\Router\get;

get('/browse', [MainController::class, 'browse'])->name('browse');
