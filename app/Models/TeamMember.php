<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
  use HasUuids;

  protected $fillable = [
    'team_id',
    'user_id',
    'role',
  ];

  public function team()
  {
    return $this->belongsTo(Team::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }
}