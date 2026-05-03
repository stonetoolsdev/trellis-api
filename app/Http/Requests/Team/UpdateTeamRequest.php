<?php

namespace App\Http\Requests\Team;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTeamRequest extends FormRequest
{
  public function authorize(): bool
  {
    return $this->user()->can('update', $this->route('team'));
  }

  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
    ];
  }
}