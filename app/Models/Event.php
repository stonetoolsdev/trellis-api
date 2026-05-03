<?php
namespace App\Models;
use App\Enums\EventFormat;
use App\Enums\LifecycleStatus;
use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Event extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'title',
    'slug',
    'description',
    'type',
    'format',
    'submission_status',
    'lifecycle_status',
    'location',
    'virtual_url',
    'start_date',
    'end_date',
    'rejection_reason',
    'submitted_by',
    'approved_by',
    'approved_at',
  ];

  protected function casts(): array
  {
    return [
      'format' => EventFormat::class,
      'submission_status' => SubmissionStatus::class,
      'lifecycle_status' => LifecycleStatus::class,
      'start_date' => 'datetime',
      'end_date' => 'datetime',
      'approved_at' => 'datetime',
    ];
  }

  protected static function booted(): void
  {
    static::creating(function (Event $event) {
      if (empty($event->slug)) {
        $slug = Str::slug($event->title);
        $count = Event::where('slug', 'like', $slug . '%')->count();
        $event->slug = $count > 0 ? $slug . '-' . ($count + 1) : $slug;
      }
    });
  }

  public function submittedBy()
  {
    return $this->belongsTo(User::class, 'submitted_by');
  }

  public function approvedBy()
  {
    return $this->belongsTo(User::class, 'approved_by');
  }

  public function teams()
  {
    return $this->belongsToMany(Team::class, 'event_teams')
      ->withTimestamps();
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
}
