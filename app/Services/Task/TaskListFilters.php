<?php

namespace App\Services\Task;

final class TaskListFilters
{
    public function __construct(
        public readonly ?string $title = null,
        public readonly ?string $status = null,
        public readonly string $dueDateSort = 'asc',
    ) {}

    /**
     * @param  array<string, mixed>  $query
     */
    public static function fromQuery(array $query): self
    {
        $title = self::normalizeOptionalText($query['title'] ?? null);
        $status = self::normalizeOptionalText($query['status'] ?? null);
        $dueDateSort = ($query['due_date_sort'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        return new self($title, $status, $dueDateSort);
    }

    private static function normalizeOptionalText(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $trimmed = trim($value);

        return $trimmed === '' ? null : $trimmed;
    }
}
