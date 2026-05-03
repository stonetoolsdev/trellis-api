<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectAssignment extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'project_id',
    'assignable_type',
    'assignable_id',
  ];

  public function assignable()
  {
    return $this->morphTo();
  }

  public function project()
  {
    return $this->belongsTo(Project::class);
  }
}