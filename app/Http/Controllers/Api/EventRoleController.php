<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EventRole;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventRoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = EventRole::orderBy('name')->get();
        return response()->json($roles);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $role = EventRole::create($request->only(['name', 'description']));
        return response()->json($role, 201);
    }

    public function show(EventRole $eventRole): JsonResponse
    {
        return response()->json($eventRole);
    }

    public function update(Request $request, EventRole $eventRole): JsonResponse
    {
        $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $eventRole->update($request->only(['name', 'description']));
        return response()->json($eventRole);
    }

    public function destroy(EventRole $eventRole): JsonResponse
    {
        $eventRole->delete();
        return response()->json(['message' => 'Event role deleted successfully']);
    }
}
