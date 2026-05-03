<?php

namespace App\Http\Requests\Event;

use App\Enums\EventFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateEventRequest extends FormRequest
{
  public function authorize(): bool
  {
    return $this->user()->can('update', $this->route('event'));
  }

  public function rules(): array
  {
    return [
      'title' => ['sometimes', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'type' => ['nullable', 'string', 'max:100'],
      'format' => ['sometimes', new Enum(EventFormat::class)],
      'location' => ['nullable', 'string', 'max:255'],
      'virtual_url' => ['nullable', 'url'],
      'start_date' => ['nullable', 'date'],
      'end_date' => ['nullable', 'date', 'after:start_date'],
      'team_ids' => ['nullable', 'array'],
      'team_ids.*' => ['uuid', 'exists:teams,id'],
    ];
  }
}