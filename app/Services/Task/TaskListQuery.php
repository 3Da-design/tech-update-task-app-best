<?php

namespace App\Services\Task;

use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

final class TaskListQuery
{
    /**
     * @return Collection<int, Task>
     */
    public function forUser(int $userId, TaskListFilters $filters): Collection
    {
        $query = Task::query()->where('user_id', $userId);

        if ($filters->title !== null) {
            $query->where('title', 'like', '%'.$this->escapeLike($filters->title).'%');
        }

        if ($filters->status !== null) {
            $query->where('status', $filters->status);
        }

        $query
            ->orderByRaw('due_date IS NULL DESC')
            ->orderBy('due_date', $filters->dueDateSort)
            ->orderBy('id');

        /** @var Collection<int, Task> */
        return $query->get();
    }

    private function escapeLike(string $value): string
    {
        return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $value);
    }
}
