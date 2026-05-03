<?php

namespace App\Http\Requests\Event;

use App\Enums\EventFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreEventRequest extends FormRequest
{
  public function authorize(): bool
  {
    return $this->user()->can('create', \App\Models\Event::class);
  }

  public function rules(): array
  {
    return [
      'title' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'type' => ['nullable', 'string', 'max:100'],
      'format' => ['required', new Enum(EventFormat::class)],
      'location' => ['nullable', 'string', 'max:255'],
      'virtual_url' => ['nullable', 'url'],
      'start_date' => ['nullable', 'date'],
      'end_date' => ['nullable', 'date', 'after:start_date'],
      'team_ids' => ['nullable', 'array'],
      'team_ids.*' => ['uuid', 'exists:teams,id'],
    ];
  }
}