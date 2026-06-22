<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Task;
use App\Services\Task\TaskService;

trait FindsAuthorizedTask
{
    abstract protected function taskService(): TaskService;

    protected function findAuthorizedTask(string $id, string $ability = 'view'): Task
    {
        $task = $this->taskService()->findById($id);
        $this->authorize($ability, $task);

        return $task;
    }
}
