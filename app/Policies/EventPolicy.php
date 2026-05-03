<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Event;
use App\Models\User;

class EventPolicy
{
  public function viewAny(User $user): bool
  {
    return true;
  }

  public function view(User $user, Event $event): bool
  {
    return true;
  }

  public function create(User $user): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin, Role::Member]);
  }

  public function update(User $user, Event $event): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin])
      || $user->id === $event->submitted_by;
  }

  public function delete(User $user, Event $event): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin]);
  }

  public function approve(User $user, Event $event): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin]);
  }

  public function reject(User $user, Event $event): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin]);
  }

  public function advanceLifecycle(User $user, Event $event): bool
  {
    return $user->hasAnyRole([Role::Owner, Role::Admin]);
  }
}