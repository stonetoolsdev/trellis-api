<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Project extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'created_by',
    'title',
    'slug',
    'description',
    'status',
  ];

  protected static function booted(): void
  {
    static::creating(function (Project $project) {
      if (empty($project->slug)) {
        $slug = Str::slug($project->title);
        $count = Project::where('slug', 'like', $slug . '%')->count();
        $project->slug = $count > 0 ? $slug . '-' . ($count + 1) : $slug;
      }
    });
  }

  public function createdBy()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function assignments()
  {
    return $this->hasMany(ProjectAssignment::class);
  }

  public function teams()
  {
    return $this->morphedByMany(Team::class, 'assignable', 'project_assignments');
  }

  public function users()
  {
    return $this->morphedByMany(User::class, 'assignable', 'project_assignments');
  }

  public function taskLists()
  {
    return $this->hasMany(TaskList::class);
  }

  public function tasks()
  {
    return $this->hasMany(Task::class);
  }

  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable')->orderBy('created_at');
  }

  public function events()
  {
    return $this->hasMany(Event::class);
  }
}