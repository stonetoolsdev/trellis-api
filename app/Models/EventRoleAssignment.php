<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRoleAssignment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'event_id',
        'event_role_id',
        'user_id',
        'notes',
        'other_description',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function eventRole()
    {
        return $this->belongsTo(EventRole::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
