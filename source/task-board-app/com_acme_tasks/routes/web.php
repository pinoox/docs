<?php

use function Pinoox\Router\{get, post};

get('/', '@task.list')->name('home');
post('/add', '@task.store')->name('task.store');
post('/done/{id}', '@task.done')->name('task.done');
post('/reopen/{id}', '@task.reopen')->name('task.reopen');
