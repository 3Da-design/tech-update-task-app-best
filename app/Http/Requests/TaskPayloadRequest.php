<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class TaskPayloadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        if ($this->has('title') && is_string($this->input('title'))) {
            $normalized['title'] = trim($this->input('title'));
        }

        if ($this->has('description') && is_string($this->input('description'))) {
            $trimmed = trim($this->input('description'));
            $normalized['description'] = $trimmed === '' ? null : $trimmed;
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    protected function taskRules(bool $partial): array
    {
        return [
            'title' => $partial
                ? ['sometimes', 'required', 'string', 'max:255']
                : ['required', 'string', 'max:255'],
            'description' => $partial
                ? ['sometimes', 'nullable', 'string']
                : ['nullable', 'string'],
            'status' => $partial
                ? ['sometimes', 'required', 'string', Rule::in(config('task.status_values'))]
                : ['required', 'string', Rule::in(config('task.status_values'))],
            'priority' => $partial
                ? ['sometimes', 'required', 'string', Rule::in(config('task.priority_values'))]
                : ['required', 'string', Rule::in(config('task.priority_values'))],
            'due_date' => $partial
                ? ['sometimes', 'nullable', 'date']
                : ['nullable', 'date'],
        ];
    }
}
