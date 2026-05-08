<?php

namespace App\Http\Controllers\Api;

use App\Enums\LifecycleStatus;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\EventCollection;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
  use AuthorizesRequests;
  public function index(Request $request): EventCollection
  {
    $events = Event::with(['submittedBy', 'approvedBy', 'teams'])
      ->where('submission_status', SubmissionStatus::Approved)
      ->when($request->lifecycle, fn($q) => $q->where('lifecycle_status', $request->lifecycle))
      ->when($request->format, fn($q) => $q->where('format', $request->format))
      ->latest()
      ->get();

    return new EventCollection($events);
  }

  public function store(StoreEventRequest $request): JsonResponse
  {
    $event = Event::create([
      ...$request->validated(),
      'submitted_by' => Auth::id(),
      'submission_status' => SubmissionStatus::Draft,
    ]);

    if ($request->team_ids) {
      $event->teams()->sync($request->team_ids);
    }

    return response()->json(
      new EventResource($event->load(['submittedBy', 'teams'])),
      201
    );
  }

  public function show(Event $event): EventResource
  {
    return new EventResource(
      $event->load([
        'submittedBy',
        'approvedBy',
        'teams',
        'taskLists.tasks.assignees',
        'taskLists.tasks.subtasks',
        'comments.user',
        'roleAssignments.eventRole',
        'roleAssignments.user',
        'inventory.inventoryItem',
      ])
    );
  }

  public function update(UpdateEventRequest $request, Event $event): EventResource
  {
    $event->update($request->validated());

    if ($request->has('team_ids')) {
      $event->teams()->sync($request->team_ids);
    }

    return new EventResource($event->load(['submittedBy', 'approvedBy', 'teams']));
  }

  public function destroy(Event $event): JsonResponse
  {
    $this->authorize('delete', $event);

    $event->delete();

    return response()->json(['message' => 'Event deleted successfully']);
  }

  public function submit(Event $event): JsonResponse
  {
    $this->authorize('update', $event);

    if ($event->submission_status !== SubmissionStatus::Draft) {
      return response()->json(['message' => 'Only draft events can be submitted'], 422);
    }

    $event->update(['submission_status' => SubmissionStatus::PendingReview]);

    return response()->json(new EventResource($event->load(['submittedBy', 'teams'])));
  }

  public function approve(Event $event): JsonResponse
  {
    $this->authorize('approve', $event);

    if ($event->submission_status !== SubmissionStatus::PendingReview) {
      return response()->json(['message' => 'Only pending events can be approved'], 422);
    }

    $event->update([
      'submission_status' => SubmissionStatus::Approved,
      'lifecycle_status' => LifecycleStatus::Planning,
      'approved_by' => Auth::id(),
      'approved_at' => now(),
    ]);

    return response()->json(new EventResource($event->load(['submittedBy', 'approvedBy', 'teams'])));
  }

  public function reject(Event $event, Request $request): JsonResponse
  {
    $this->authorize('reject', $event);

    if ($event->submission_status !== SubmissionStatus::PendingReview) {
      return response()->json(['message' => 'Only pending events can be rejected'], 422);
    }

    $request->validate(['reason' => ['required', 'string']]);

    $event->update([
      'submission_status' => SubmissionStatus::Rejected,
      'rejection_reason' => $request->reason,
    ]);

    return response()->json(new EventResource($event->load(['submittedBy', 'teams'])));
  }

  public function advanceLifecycle(Event $event): JsonResponse
  {
    $this->authorize('advanceLifecycle', $event);

    if ($event->submission_status !== SubmissionStatus::Approved) {
      return response()->json(['message' => 'Event must be approved first'], 422);
    }

    $transitions = [
      LifecycleStatus::Planning->value => LifecycleStatus::InProgress,
      LifecycleStatus::InProgress->value => LifecycleStatus::Post,
      LifecycleStatus::Post->value => LifecycleStatus::Completed,
    ];

    $current = $event->lifecycle_status->value;

    if (!isset($transitions[$current])) {
      return response()->json(['message' => 'Event is already completed'], 422);
    }

    $event->update(['lifecycle_status' => $transitions[$current]]);

    return response()->json(new EventResource($event->load(['submittedBy', 'approvedBy', 'teams'])));
  }

  public function assignTeams(Request $request, Event $event): JsonResponse
  {
    $this->authorize('update', $event);

    $request->validate([
      'team_ids' => ['required', 'array'],
      'team_ids.*' => ['exists:teams,id'],
    ]);

    $event->teams()->syncWithoutDetaching($request->team_ids);

    return response()->json(new EventResource($event->load(['submittedBy', 'approvedBy', 'teams'])));
  }

  public function removeTeams(Request $request, Event $event): JsonResponse
  {
    $this->authorize('update', $event);

    $request->validate([
      'team_ids' => ['required', 'array'],
      'team_ids.*' => ['exists:teams,id'],
    ]);

    $event->teams()->detach($request->team_ids);

    return response()->json(new EventResource($event->load(['submittedBy', 'approvedBy', 'teams'])));
  }

  public function mine(Request $request): JsonResponse
  {
    $events = Event::where('submitted_by', Auth::id())
      ->when($request->status, fn($q) => $q->where('submission_status', $request->status))
      ->with(['submittedBy', 'teams'])
      ->latest()
      ->get();

    return response()->json(['data' => EventResource::collection($events)]);
  }

  public function submissions(Request $request): JsonResponse
  {
    $events = Event::with(['approvedBy', 'teams'])
      ->when($request->status, fn($q) => $q->where('submission_status', $request->status))
      ->latest()
      ->get();

    return response()->json(['data' => EventResource::collection(($events))]);
  }

  public function uploadPhoto(Request $request, Event $event): JsonResponse
  {
    $request->validate([
      'photo' => ['required', 'image', 'max:5120'],
    ]);

    if ($event->featured_photo_path) {
      Storage::disk('public')->delete($event->featured_photo_path);
    }

    $path = $request->file('photo')->store('event-photos', 'public');
    $event->update(['featured_photo_path' => $path]);

    return response()->json([
      'featured_photo_url' => env('APP_URL') . '/storage/' . $path,
    ]);
  }
}