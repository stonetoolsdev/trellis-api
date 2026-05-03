<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class EventObserver
{
  public function created(Event $event): void
  {
    ActivityLog::create([
      'user_id' => Auth::id(),
      'event_id' => $event->id,
      'action' => 'event_created',
      'payload' => [
        'title' => $event->title,
        'submission_status' => $event->submission_status,
      ],
    ]);
  }

  public function updated(Event $event): void
  {
    $changes = $event->getChanges();
    $original = $event->getOriginal();

    if (isset($changes['submission_status'])) {
      ActivityLog::create([
        'user_id' => Auth::id(),
        'event_id' => $event->id,
        'action' => 'submission_status_changed',
        'payload' => [
          'from' => $original['submission_status'],
          'to' => $changes['submission_status'],
        ],
      ]);
    }

    if (isset($changes['lifecycle_status'])) {
      ActivityLog::create([
        'user_id' => Auth::id(),
        'event_id' => $event->id,
        'action' => 'lifecycle_status_changed',
        'payload' => [
          'from' => $original['lifecycle_status'],
          'to' => $changes['lifecycle_status'],
        ],
      ]);
    }
  }

  public function deleted(Event $event): void
  {
    ActivityLog::create([
      'user_id' => Auth::id(),
      'event_id' => null,
      'action' => 'event_deleted',
      'payload' => [
        'title' => $event->title,
      ],
    ]);
  }
}