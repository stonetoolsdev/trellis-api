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
      'title' => $this->title,
      'slug' => $this->slug,
      'description' => $this->description,
      'type' => $this->type,
      'format' => $this->format,
      'submission_status' => $this->submission_status,
      'lifecycle_status' => $this->lifecycle_status,
      'location' => $this->location,
      'virtual_url' => $this->virtual_url,
      'start_date' => $this->start_date,
      'end_date' => $this->end_date,
      'rejection_reason' => $this->rejection_reason,
      'submitted_by' => new UserResource($this->whenLoaded('submittedBy')),
      'approved_by' => new UserResource($this->whenLoaded('approvedBy')),
      'approved_at' => $this->approved_at,
      'teams' => TeamResource::collection($this->whenLoaded('teams')),
      'task_lists' => TaskListResource::collection($this->whenLoaded('taskLists')),
      'created_at' => $this->created_at,
      'comments' => $this->whenLoaded('comments', function () {
        return $this->comments->map(fn($comment) => [
          'id' => $comment->id,
          'body' => $comment->body,
          'user' => new UserResource($comment->user),
          'created_at' => $comment->created_at,
        ]);
      }),
    ];
  }
}