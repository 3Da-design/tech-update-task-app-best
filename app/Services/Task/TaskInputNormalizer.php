<?php

namespace App\Services\Task;

final class TaskInputNormalizer
{
    private const ALLOWED_KEYS = ['title', 'description', 'status', 'priority', 'due_date'];

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function normalize(array $data): array
    {
        $normalized = array_intersect_key($data, array_flip(self::ALLOWED_KEYS));

        if (array_key_exists('description', $normalized)) {
            $normalized['description'] = $this->normalizeDescription($normalized['description']);
        }

        return $normalized;
    }

    private function normalizeDescription(mixed $description): ?string
    {
        if ($description === null || $description === '') {
            return null;
        }

        if (! is_string($description)) {
            return null;
        }

        $trimmed = trim($description);

        return $trimmed === '' ? null : $trimmed;
    }
}
