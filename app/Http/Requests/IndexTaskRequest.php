<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTaskRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    $normalized = [];

    foreach (['title', 'status', 'due_date_sort'] as $key) {
      if (! $this->has($key)) {
        continue;
      }

      $value = $this->input($key);
      if (! is_string($value)) {
        continue;
      }

      $trimmed = trim($value);
      $normalized[$key] = $trimmed === '' ? null : $trimmed;
    }

    if (array_key_exists('due_date_sort', $normalized) && $normalized['due_date_sort'] !== null) {
      if ($normalized['due_date_sort'] !== 'asc' && $normalized['due_date_sort'] !== 'desc') {
        $normalized['due_date_sort'] = null;
      }
    }

    if ($normalized !== []) {
      $this->merge($normalized);
    }
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title' => ['nullable', 'string', 'max:255'],
      'status' => ['nullable', 'string', Rule::in(config('task.status_values'))],
      'due_date_sort' => ['nullable', 'string', Rule::in(['asc', 'desc'])],
    ];
  }
}
