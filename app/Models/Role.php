<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
  use HasUuids;

  protected $fillable = ['name', 'permissions'];

  protected function casts(): array
  {
    return [
      'permissions' => 'array',
    ];
  }

  public function users()
  {
    return $this->belongsToMany(User::class, 'user_roles');
  }
}