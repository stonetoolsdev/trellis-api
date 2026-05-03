<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
  use HasUuids;

  protected $fillable = [
    'user_id',
    'event_id',
    'action',
    'payload',
  ];

  protected function casts(): array
  {
    return [
      'payload' => 'array',
    ];
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function event()
  {
    return $this->belongsTo(Event::class);
  }
}