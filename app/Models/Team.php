<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Team extends Model
{
  use HasFactory, HasUuids;

  protected $hidden = ['pivot'];

  protected $fillable = [
    'name',
    'slug',
    'description',
    'color',
  ];

  protected static function booted(): void
  {
    static::creating(function (Team $team) {
      if (empty($team->slug)) {
        $team->slug = Str::slug($team->name);
      }
    });
  }

  public function members()
  {
    return $this->hasMany(TeamMember::class);
  }

  public function users()
  {
    return $this->belongsToMany(User::class, 'team_members')
      ->withPivot('role')
      ->withTimestamps();
  }
}