<?php
namespace App\com_acme_react_tasks\Model;

use Pinoox\Component\Database\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $fillable = ['title', 'status'];
}
