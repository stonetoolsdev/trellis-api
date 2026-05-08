<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
  public function toArray(Request $request): array
  {
    return [
      'id' => $this->id,
      'project_id' => $this->project_id,
      'title' => $this->title,
      'slug' => $this->slug,
      'description' => $this->description,
      'goals' => $this->goals,
      'type' => $this->type,
      'format' => $this->format,
      'submission_status' => $this->submission_status,
      'lifecycle_status' => $this->lifecycle_status,
      'location' => $this->location,
      'virtual_url' => $this->virtual_url,
      'featured_photo_url' => $this->featured_photo_path
        ? env('APP_URL') . '/storage/' . $this->featured_photo_path
        : null,
      'start_date' => $this->start_date,
      'end_date' => $this->end_date,
      'rejection_reason' => $this->rejection_reason,
      'submitted_by' => new UserResource($this->whenLoaded('submittedBy')),
      'approved_by' => new UserResource($this->whenLoaded('approvedBy')),
      'approved_at' => $this->approved_at,
      'teams' => TeamResource::collection($this->whenLoaded('teams')),
      'task_lists' => TaskListResource::collection($this->whenLoaded('taskLists')),
      'comments' => $this->whenLoaded('comments', function () {
        return $this->comments->map(fn($comment) => [
          'id' => $comment->id,
          'body' => $comment->body,
          'user' => new UserResource($comment->user),
          'created_at' => $comment->created_at,
        ]);
      }),
      'created_at' => $this->created_at,
      'role_assignments' => $this->whenLoaded('roleAssignments', function () {
        return $this->roleAssignments->map(fn($a) => [
          'id' => $a->id,
          'event_role' => $a->eventRole ? ['id' => $a->eventRole->id, 'name' => $a->eventRole->name] : null,
          'user' => $a->user ? ['id' => $a->user->id, 'name' => $a->user->name] : null,
          'notes' => $a->notes,
          'other_description' => $a->other_description,
        ]);
      }),
      'inventory' => $this->whenLoaded('inventory', function () {
        return $this->inventory->map(fn($i) => [
          'id' => $i->id,
          'inventory_item' => $i->inventoryItem ? ['id' => $i->inventoryItem->id, 'name' => $i->inventoryItem->name] : null,
          'quantity_needed' => $i->quantity_needed,
          'notes' => $i->notes,
          'other_description' => $i->other_description,
        ]);
      }),
    ];
  }
}
