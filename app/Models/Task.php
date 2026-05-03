<?php

namespace App\Models;

use App\Enums\TaskStatus;
use App\Enums\TaskPriority;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'user_id',
    'event_id',
    'project_id',
    'task_list_id',
    'parent_id',
    'title',
    'description',
    'status',
    'priority',
    'due_date',
  ];

  protected function casts(): array
  {
    return [
      'due_date' => 'date',
      'status' => TaskStatus::class,
      'priority' => TaskPriority::class,
    ];
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function assignees()
  {
    return $this->belongsToMany(User::class, 'task_assignees')->withTimestamps();
  }

  public function event()
  {
    return $this->belongsTo(Event::class);
  }

  public function taskList()
  {
    return $this->belongsTo(TaskList::class);
  }

  public function parent()
  {
    return $this->belongsTo(Task::class, 'parent_id');
  }

  public function subtasks()
  {
    return $this->hasMany(Task::class, 'parent_id');
  }

  public function comments()
  {
    return $this->morphMany(Comment::class, 'commentable')->orderBy('created_at');
  }
}
