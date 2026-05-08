<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRoleAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventRoleAssignmentController extends Controller
{
    public function index(Event $event): JsonResponse
    {
        $assignments = $event->roleAssignments()
            ->with(['eventRole', 'user'])
            ->get();
        return response()->json($assignments);
    }

    public function store(Request $request, Event $event): JsonResponse
    {
        $request->validate([
            'event_role_id' => ['nullable', 'exists:event_roles,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'other_description' => ['nullable', 'string', 'required_without:event_role_id'],
        ]);

        $assignment = $event->roleAssignments()->create([
            'event_role_id' => $request->event_role_id,
            'user_id' => $request->user_id,
            'notes' => $request->notes,
            'other_description' => $request->other_description,
        ]);

        return response()->json($assignment->load(['eventRole', 'user']), 201);
    }

    public function update(Request $request, Event $event, EventRoleAssignment $assignment): JsonResponse
    {
        $request->validate([
            'event_role_id' => ['nullable', 'exists:event_roles,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
            'other_description' => ['nullable', 'string'],
        ]);

        $assignment->update($request->only([
            'event_role_id', 'user_id', 'notes', 'other_description'
        ]));

        return response()->json($assignment->load(['eventRole', 'user']));
    }

    public function destroy(Event $event, EventRoleAssignment $assignment): JsonResponse
    {
        $assignment->delete();
        return response()->json(['message' => 'Assignment removed']);
    }
}
