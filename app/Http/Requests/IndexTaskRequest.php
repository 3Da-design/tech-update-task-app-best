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

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title' => ['sometimes', 'string', 'max:255'],
      'status' => ['sometimes', 'string', Rule::in(config('task.status_values'))],
      'due_date_sort' => ['sometimes', 'string', 'in:asc,desc'],
    ];
  }
}
