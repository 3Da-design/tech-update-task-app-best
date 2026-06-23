<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class TaskService
{
    public function __construct(
        private readonly TaskListQuery $taskListQuery,
        private readonly TaskInputNormalizer $taskInputNormalizer,
    ) {}

    /**
     * @return Collection<int, Task>
     */
    public function listForUser(int $userId, TaskListFilters $filters): Collection
    {
        return $this->taskListQuery->forUser($userId, $filters);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function createForUser(int $userId, array $attributes): Task
    {
        $task = new Task;
        $task->fill($this->taskInputNormalizer->normalize($attributes));
        $task->user_id = $userId;
        $task->save();

        return $task->fresh() ?? $task;
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    public function update(Task $task, array $attributes): Task
    {
        $task->fill($this->taskInputNormalizer->normalize($attributes));
        $task->save();

        return $task->fresh() ?? $task;
    }

    public function delete(Task $task): void
    {
        $task->delete();
    }

    public function findById(string $id): Task
    {
        if (! ctype_digit($id)) {
            throw (new ModelNotFoundException)->setModel(Task::class, [$id]);
        }

        /** @var Task|null $task */
        $task = Task::query()->whereKey((int) $id)->first();

        if ($task === null) {
            throw (new ModelNotFoundException)->setModel(Task::class, [$id]);
        }

        return $task;
    }
}
