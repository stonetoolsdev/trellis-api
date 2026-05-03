<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, HasUuids, Notifiable;

  protected $fillable = [
    'name',
    'email',
    'password',
    'avatar_url',
    'avatar_path',
    'pronouns',
    'timezone',
    'is_active',
  ];

  protected $hidden = [
    'password',
    'remember_token',
    'pivot',
    'email_verified_at',
    'updated_at',
  ];

  protected $appends = ['avatar'];

  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
      'is_active' => 'boolean',
      'pronouns' => 'array',
    ];
  }

  public function getAvatarAttribute(): string|null
  {
    if ($this->avatar_path) {
      return env('APP_URL', 'http://localhost:8000') . '/storage/' . $this->avatar_path;
    }
    return $this->avatar_url;
  }

  public function roles()
  {
    return $this->belongsToMany(Role::class, 'user_roles');
  }

  public function hasRole(Role $role): bool
  {
    return $this->roles->contains('name', $role->value);
  }

  public function hasAnyRole(array $roles): bool
  {
    return $this->roles->whereIn('name', collect($roles)->map->value->all())->isNotEmpty();
  }

  public function teams()
  {
    return $this->belongsToMany(Team::class, 'team_members')
      ->withPivot('role')
      ->withTimestamps();
  }

  public function submittedEvents()
  {
    return $this->hasMany(Event::class, 'submitted_by');
  }

  public function approvedEvents()
  {
    return $this->hasMany(Event::class, 'approved_by');
  }

  public function tasks()
  {
    return $this->hasMany(Task::class);
  }

  public function assignedTasks()
  {
    return $this->belongsToMany(Task::class, 'task_assignees')->withTimestamps();
  }
}
