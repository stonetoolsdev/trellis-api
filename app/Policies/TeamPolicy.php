<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
  public function viewAny(User $user): bool
  {
    return true;
  }

  public function view(User $user, Team $team): bool
  {
    return true;
  }

  public function create(User $user): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin]);
  }

  public function update(User $user, Team $team): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin]);
  }

  public function delete(User $user, Team $team): bool
  {
    return $user->hasRole(Role::Owner);
  }

  public function manageMembers(User $user, Team $team): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin]);
  }
}