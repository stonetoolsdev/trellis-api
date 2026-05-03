<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
  public function viewAny(User $user): bool
  {
    return true;
  }

  public function view(User $user, Task $task): bool
  {
    return true;
  }

  public function create(User $user): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin, Role::Member]);
  }

  public function update(User $user, Task $task): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin])
      || $user->id === $task->created_by
      || $user->id === $task->assigned_to;
  }

  public function delete(User $user, Task $task): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin])
      || $user->id === $task->created_by;
  }

  public function reorder(User $user): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin, Role::Member]);
  }
}