<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
  public function index(): JsonResponse
  {
    $projects = Project::with(['createdBy', 'teams', 'users'])
      ->latest()
      ->get();

    return response()->json($projects);
  }

  public function store(Request $request): JsonResponse
  {
    $request->validate([
      'title' => ['required', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'status' => ['sometimes', 'in:active,on_hold,archived'],
      'team_ids' => ['nullable', 'array'],
      'team_ids.*' => ['exists:teams,id'],
      'user_ids' => ['nullable', 'array'],
      'user_ids.*' => ['exists:users,id'],
    ]);

    $project = Project::create([
      'title' => $request->title,
      'description' => $request->description,
      'status' => $request->input('status', 'active'),
      'created_by' => Auth::id(),
    ]);

    if ($request->team_ids) {
      foreach ($request->team_ids as $teamId) {
        $project->assignments()->create([
          'assignable_type' => 'App\Models\Team',
          'assignable_id' => $teamId,
        ]);
      }
    }

    if ($request->user_ids) {
      foreach ($request->user_ids as $userId) {
        $project->assignments()->create([
          'assignable_type' => 'App\Models\User',
          'assignable_id' => $userId,
        ]);
      }
    }

    return response()->json($project->load(['createdBy', 'teams', 'users']), 201);
  }

  public function show(Project $project): JsonResponse
  {
    return response()->json(
      $project->load([
        'createdBy',
        'teams',
        'users',
        'taskLists.tasks.assignees',
        'taskLists.tasks.subtasks',
        'comments.user'
      ])
    );
  }

  public function update(Request $request, Project $project): JsonResponse
  {
    $request->validate([
      'title' => ['sometimes', 'string', 'max:255'],
      'description' => ['nullable', 'string'],
      'status' => ['sometimes', 'in:active,on_hold,archived'],
      'team_ids' => ['nullable', 'array'],
      'team_ids.*' => ['exists:teams,id'],
      'user_ids' => ['nullable', 'array'],
      'user_ids.*' => ['exists:users,id'],
    ]);

    $project->update($request->only(['title', 'description', 'status']));

    if ($request->has('team_ids')) {
      $project->assignments()->where('assignable_type', 'App\Models\Team')->delete();
      foreach ($request->team_ids as $teamId) {
        $project->assignments()->create([
          'assignable_type' => 'App\Models\Team',
          'assignable_id' => $teamId,
        ]);
      }
    }

    if ($request->has('user_ids')) {
      $project->assignments()->where('assignable_type', 'App\Models\User')->delete();
      foreach ($request->user_ids as $userId) {
        $project->assignments()->create([
          'assignable_type' => 'App\Models\User',
          'assignable_id' => $userId,
        ]);
      }
    }

    return response()->json($project->load(['createdBy', 'teams', 'users']));
  }

  public function destroy(Project $project): JsonResponse
  {
    $project->delete();

    return response()->json(['message' => 'Project deleted successfully']);
  }
}